<?php

namespace App\Test\EnvayaSms;

use App\EnvayaSms\Authenticator;
use App\EnvayaSms\Response;

class AuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateSignatureSuccess()
    {
        $a = new Authenticator('PASSWORD');
        $postData = [
            "action" => "test",
            "version" => "30",
            "phone_number" => "12345678",
            "phone_id" => "",
            "phone_token" => "",
            "send_limit" => "100",
            "now" => "1457031878743",
            "settings_version" => "0",
            "battery" => "84",
            "power" => "0",
            "network" => "WIFI",
            "log" => "[8:03:34 PM]\nPassword changed\n[8:04:17 PM]\nServer URL changed to: http://requestb.in/1419fn21\nTesting server connection...\n",
        ];

        $this->assertTrue($a->validateSignature(
            'http://example.com/sms',
            'zSGtengmnwUcpT8ziyxpuTfQS0g=',
            new Response($postData)
        ));
    }

    public function testValidateSignatureFail()
    {
        $a = new Authenticator('ABC');
        $this->assertFalse($a->validateSignature('123', '456', new Response([])));
    }
}
