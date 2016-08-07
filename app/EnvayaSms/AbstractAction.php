<?php

namespace App\EnvayaSms;

abstract class AbstractAction
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getPhoneNumber()
    {
        return $this->data['phone_number'];
    }

    public function getMessageList()
    {
        $text = trim($this->data['log']);
        if (empty($text)) {
            return [];
        }

        return array_map(function($a) {
            return trim($a);
        }, explode("\n", $text));
    }

    public function getNow()
    {
        return (new \DateTime())->setTimestamp($this->data['now']/1000);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function getId()
    {
        $keys = ['action', 'phone_number', 'now', 'message', 'timestamp', 'from'];
        $str = json_encode(array_intersect_key($this->data, array_fill_keys($keys, null)));

        return substr(sha1($str), -16);
    }
}
