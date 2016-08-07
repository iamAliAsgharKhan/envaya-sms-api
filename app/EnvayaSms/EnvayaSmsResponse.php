<?php

namespace App\EnvayaSms;

class EnvayaSmsResponse
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getActionObject()
    {
        switch ($this->data['action']) {
            case 'incoming':
                return new IncomingAction($this->data);
            case 'outgoing':
                return new OutgoingAction($this->data);
            case 'send_status':
                return new SendStatusAction($this->data);
            case 'test':
                return new TestAction($this->data);
        }

        throw new UnknownActionException("Unknown action: `{$this->data['action']}`");
    }

    public function toArray()
    {
        return $this->data;
    }
}
