<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;

class ApiController extends AbstractController
{
    public function submitAction(Request $request, ResponseInterface $response, $args)
    {
        $tokens = $this->settings('api.tokens', []);
        $input = $request->getHeaderLine('Authorization');
        if (strpos($input, 'Basic') !== 0 || !in_array(substr($input, 6), $tokens)) {
            $response->getBody()->write(json_encode(['status' => 'Invalid auth token']));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(403, 'Invalid auth token');
        }

        $data = [
            'to' => $request->getParam('recipient'),
            'message' => $request->getParam('text'),
        ];
        $id = substr(md5(json_encode($data).time()), 4, 16);
        $this->log(
            \Monolog\Logger::INFO,
            sprintf('Adds message: %s', $request->getParam('text')),
            ['phone' => $args['phone'], 'to' => $request->getParam('recipient'), 'id' => $id]
        );

        $this->container->get('redis')->lPush(
            sprintf('sms:%s:outgoing', $args['phone']),
            json_encode(array_merge(compact('id'), $data))
        );

        $response->getBody()->write(json_encode(['status' => 'ok', 'id' => $id, 'data' => $data]));
        return $response
            ->withHeader('Content-type', 'application/json');
    }
}
