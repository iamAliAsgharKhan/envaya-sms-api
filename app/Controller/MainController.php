<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;

class MainController extends AbstractController
{
    public function indexAction(Request $request, ResponseInterface $response)
    {
        $response->getBody()->write('SMS');

        return $response;
    }

    public function testAction(Request $request, ResponseInterface $response)
    {
        $response->getBody()->write('OK');
        return $response;
    }
}
