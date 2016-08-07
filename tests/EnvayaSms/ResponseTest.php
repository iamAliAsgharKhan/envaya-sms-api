<?php

namespace App\Test\EnvayaSms;

use App\EnvayaSms\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $data = [
            "action" => "test",
            "version" => 30,
            "phone_number" => "12345678",
            "phone_id" => 123,
            "phone_token" => false,
        ];
        $this->assertSame($data, (new Response($data))->toArray());
    }

    public function testGetActionObjectReturn()
    {
        foreach (['incoming', 'outgoing', 'test'] as $t) {
            $this->assertInstanceOf(
                sprintf('\App\EnvayaSms\%sAction', ucfirst($t)),
                (new Response(['action' => $t]))->getActionObject()
            );
        }
    }

    /**
     * @expectedException        \App\EnvayaSms\UnknownActionException
     * @expectedExceptionMessage Unknown action: `unknown`
     */
    public function testUnknownActionException()
    {
        (new Response(['action' => 'unknown']))->getActionObject();
    }

}
