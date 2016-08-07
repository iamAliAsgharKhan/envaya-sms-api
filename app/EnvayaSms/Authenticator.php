<?php

namespace App\EnvayaSms;

class Authenticator
{
    protected $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function validateSignature($url, $signature, EnvayaSmsResponse $response)
    {
        $r = [$url];

        $params = $response->toArray();
        if (empty($params)) {
            return false;
        }

        ksort($params);
        foreach ($params as $k => $v) {
            $r[] = "$k=$v";
        }

        $r[] = $this->password;

        return $signature === base64_encode(sha1(implode(',', $r), true));
    }
}
