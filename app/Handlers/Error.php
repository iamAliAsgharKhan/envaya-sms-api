<?php

namespace App\Handlers;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Error extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct($displayErrorDetails = false, LoggerInterface $logger)
    {
        $this->displayErrorDetails = (bool)$displayErrorDetails;
        $this->logger = $logger;
    }

    protected function writeToErrorLog($exception)
    {
        $context = [
            'exception' => true,
            'file' => $exception->getFile() . ':' . $exception->getLine(),
            'stackTrace' => $exception->getTraceAsString(),
            'trace' => $exception->getTrace(),
        ];
        $this->logger->addRecord(
            Logger::CRITICAL,
            sprintf('Exception: %s', $exception->getMessage()),
            $context
        );
    }
}
