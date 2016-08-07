<?php

namespace App\Controller;

use Slim\Collection;
use Slim\Container;

class AbstractController
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function log($level, $message, array $context = [])
    {
        return $this->container->get('logger')->addRecord($level, $message, $context);
    }

    protected function settings($key, $default = null)
    {
        $c = $this->container->get('settings');

        $list = explode('.', $key);
        $x = count($list);
        foreach ($list as $i => $e) {
            if (!$c->has($e)) {
                return $default;
            }

            if (is_array($c->get($e)) && $x != ($i+1)) {
                $c = new Collection($c->get($e));
                continue;
            }

            return $c->get($e);
        }

        return $default;
    }
}
