<?php

namespace App\Test\EnvayaSms;

use App\EnvayaSms\IncomingAction;

class IncomingActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->action = new IncomingAction([
            "action" => "incoming",
            "version" => "30",
            "phone_number" => "12345678",
            "send_limit" => "100",
            "now" => "1456957902564",
            "settings_version" => "0",
            "battery" => "88",
            "power" => "0",
            "network" => "WIFI",
            "log" => "Received SMS\n",

            "message_type" => "sms",
            "message" => "Ping!\n",
            "timestamp" => "1456957898000",
            "from" => "87654321",
        ]);
    }

    public function testGetPhoneNumber()
    {
        $this->assertSame('12345678', $this->action->getPhoneNumber());
    }

    public function testGetMessageType()
    {
        $this->assertSame(IncomingAction::TYPE_SMS, $this->action->getMessageType());
    }

    public function testGetMessage()
    {
        $this->assertSame('Ping!', $this->action->getMessage());
    }

    public function testGetTimestamp()
    {
        $this->assertSame('2016-03-02T22:31:38+00:00', $this->action->getTimestamp()->format('c'));
    }

    public function testGetFrom()
    {
        $this->assertSame('87654321', $this->action->getFrom());
    }
}
