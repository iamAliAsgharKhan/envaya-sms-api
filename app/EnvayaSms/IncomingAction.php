<?php

namespace App\EnvayaSms;

class IncomingAction extends AbstractAction
{
    const TYPE_SMS = 'sms';

    public function getMessageType()
    {
        return $this->data['message_type'];
    }

    public function getMessage()
    {
        return trim($this->data['message']);
    }

    public function getTimestamp()
    {
        return (new \DateTime())->setTimestamp($this->data['timestamp']/1000);
    }

    public function getFrom()
    {
        return trim($this->data['from']);
    }
}
