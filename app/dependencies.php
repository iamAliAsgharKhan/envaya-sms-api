<?php

$c = $app->getContainer();

$c['logger'] = function (Slim\Container $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    if (ENV == 'production') {
        $logger->pushHandler(new Monolog\Handler\SyslogHandler($settings['name'], LOG_USER, Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    }

    return $logger;
};

$c['redis'] = function (\Slim\Container $c) {
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);

    return $redis;
};

$c['errorHandler'] = function ($c) {
    return new App\Handlers\Error(
        $c->get('settings')['displayErrorDetails'],
        $c['logger']
    );
};
