<?php

define('ENV', gethostname() == 'server.blamh.com' ? 'production' : 'development');
CONST APP_ROOT = __DIR__ . '/..';

require APP_ROOT . '/vendor/autoload.php';

$settings = require APP_ROOT . '/app/settings.php';
$app = new \Slim\App($settings);

require APP_ROOT . '/app/dependencies.php';
require APP_ROOT . '/app/routes.php';

$app->run();
