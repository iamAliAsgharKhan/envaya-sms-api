<?php

namespace App\EnvayaSms;

class SendStatusAction extends AbstractAction
{
    public function getStatus()
    {
        return trim($this->data['status']);
    }

    public function getError()
    {
        return trim($this->data['error']);
    }

    public function getId()
    {
        return trim($this->data['id']);
    }
}
