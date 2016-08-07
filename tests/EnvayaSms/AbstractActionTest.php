<?php

namespace App\Test\EnvayaSms;

use App\EnvayaSms\AbstractAction;

class ThisAction extends AbstractAction {}

class AbstractActionTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyLogEntries()
    {
        $this->assertSame([], (new ThisAction(['log' => ""]))->getMessageList());
        $this->assertSame([], (new ThisAction(['log' => "\n"]))->getMessageList());
    }

    public function testFirstLogEntries()
    {
        $logText = "Server connection OK!\n";
        $a = new ThisAction(['log' => $logText]);

        $expected = [
            'Server connection OK!',
        ];
        $this->assertSame($expected, $a->getMessageList());
    }

    public function testTwoLogEntries()
    {
        $logText = "Server connection OK!\nRetrying forwarding SMS from 12345678\n";
        $a = new ThisAction(['log' => $logText]);

        $expected = [
            'Server connection OK!',
            'Retrying forwarding SMS from 12345678',
        ];
        $this->assertSame($expected, $a->getMessageList());
    }

    public function testGetNow()
    {
        $a = new ThisAction(['now' => '1456957902564']);
        $this->assertSame('2016-03-02T22:31:42+00:00', $a->getNow()->format('c'));
    }

    public function testGetId()
    {
        $d = [
            'action' => 'outgoing',
            'version' => '30',
            'phone_number' => '+4561275680',
            'send_limit' => '100',
            'now' => '1456957826847',
            'settings_version' => '0',
            'battery' => '88',
            'power' => '0',
            'network' => 'WIFI',
            'log' => "[11:30:26 PM]\n  Checking for messages\n",
        ];
        $a = new ThisAction($d);
        $this->assertSame('bf2006c7331a7b0b', $a->getId());
    }
}
