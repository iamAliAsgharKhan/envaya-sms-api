<?php

namespace App\Controller;

use App\EnvayaSms\Authenticator;
use App\EnvayaSms\EnvayaSmsResponse;
use App\EnvayaSms\IncomingAction;
use App\EnvayaSms\OutgoingAction;
use App\EnvayaSms\SendStatusAction;
use App\EnvayaSms\TestAction;
use App\EnvayaSms\UnknownActionException;
use Datadogstatsd;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;

class CatchController extends AbstractController
{
    public function indexAction(Request $request, ResponseInterface $response)
    {
        $smsResponse = new EnvayaSmsResponse($request->getParsedBody());
        $url = $this->settings('envaya_sms.url', (string) $request->getUri());
        $signature = $request->getHeaderLine('X-Request-Signature');

        $auth = new Authenticator($this->settings('envaya_sms.password'));
        if (!$auth->validateSignature($url, $signature, $smsResponse)) {
            $this->log(\Monolog\Logger::NOTICE, 'Invalid signature', [
                'response' => $smsResponse->toArray(),
                'url' => $url,
                'request-signature' => $signature,
            ]);
            return $response->withStatus(403, 'Invalid signature');
        }

        try {
            $obj = $smsResponse->getActionObject();
        } catch (UnknownActionException $e) {
            $this->log(\Monolog\Logger::ERROR, $e->getMessage(), [
                'response' => $smsResponse->toArray(),
                'url' => $url,
                'request-signature' => $signature,
            ]);
            return $response->withStatus(403, $e->getMessage());
        }

        $redis = $this->container->get('redis');
        $redis->set(sprintf('sms:%s:last_update', $obj->getPhoneNumber()), date('c'));
        $redis->publish('sms:update', sprintf('%s:last_update', $obj->getPhoneNumber()));
        foreach ($obj->getMessageList() as $msg) {
            $redis->publish(sprintf('sms:%s:log', $obj->getPhoneNumber()), $msg);
            Datadogstatsd::increment('sms.log', 1, ['phone' => $obj->getPhoneNumber()]);
            $this->log(
                \Monolog\Logger::INFO,
                $msg,
                ['phone' => $obj->getPhoneNumber(), 'id'=> $obj->getId(), 'log' => true]
            );
        }

        if ($obj instanceof TestAction) {
            return $response;
        }

        if ($obj instanceof IncomingAction) {
            return $this->incomingAction($obj, $request, $response);
        }

        if ($obj instanceof OutgoingAction) {
            return $this->outgoingAction($obj, $request, $response);
        }

        if ($obj instanceof SendStatusAction) {
            return $this->sendStatusAction($obj, $request, $response);
        }

        return $response->withStatus(403, get_class($obj));
    }

    protected function incomingAction(IncomingAction $obj, Request $request, ResponseInterface $response)
    {
        $data = [
            'id' => $obj->getId(),
            'from' => $obj->getFrom(),
            'to' => $obj->getPhoneNumber(),
            'message' => $obj->getMessage(),
            'timestamp' => $obj->getNow()->format('c'),
        ];
        $this->log(\Monolog\Logger::INFO, 'Incoming action', $data);

        $redis = $this->container->get('redis');
        $redis->lpush(sprintf('sms:%s:incoming', $obj->getPhoneNumber()), json_encode($data));
        $redis->publish('sms:update', sprintf('%s:incoming', $obj->getPhoneNumber()));

        Datadogstatsd::set('sms.incoming', 1, ['phone' => $obj->getPhoneNumber()]);

        $response->getBody()->write('{"events":[]}');
        return $response
            ->withHeader('Content-type', 'application/json');
    }

    protected function outgoingAction(OutgoingAction $obj, Request $request, ResponseInterface $response)
    {
        $redis = $this->container->get('redis');
        $messages = [];
        while (1) {
            $r = $redis->rPop(sprintf('sms:%s:outgoing', $obj->getPhoneNumber()));
            if (!$r) {
                break;
            }

            $messages[] = json_decode($r, true);

            Datadogstatsd::increment('sms.outgoing', 1, ['phone' => $obj->getPhoneNumber()]);

            // Limit the list of messages
            if (10 == count($messages)) {
                break;
            }
        }

        $r = ['events' => []];
        if (!empty($messages)) {
            $r = ['events' => [['event' => 'send', 'messages' => $messages]]];
        }
        $this->log(\Monolog\Logger::INFO, 'Outgoing action', compact('messages'));
        $response->getBody()->write(json_encode($r));

        return $response
            ->withHeader('Content-type', 'application/json');
    }

    protected function sendStatusAction(SendStatusAction $obj, Request $request, ResponseInterface $response)
    {
        $redis = $this->container->get('redis');
        $redis->publish(sprintf('sms:%s:send-status', $obj->getPhoneNumber()), $obj->getId());

        Datadogstatsd::set('sms.status', 1, ['phone' => $obj->getPhoneNumber()]);

        $response->getBody()->write('{"events":[]}');
        return $response
            ->withHeader('Content-type', 'application/json');
    }
}
