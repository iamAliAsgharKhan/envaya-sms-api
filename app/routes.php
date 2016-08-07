<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;

$app->get('/', 'App\Controller\MainController:indexAction');
$app->get('/test', 'App\Controller\MainController:testAction');
$app->post('/catch', 'App\Controller\CatchController:indexAction');
$app->post('/api/{phone}/message', 'App\Controller\ApiController:submitAction');

$app->add(function (Request $request, ResponseInterface $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        return $response->withRedirect((string)$uri, 301);
    }

    return $next($request, $response);
});
