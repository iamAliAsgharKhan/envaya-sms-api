Simple API for working with EnvayaSMS
=====================================

From the [EnvayaSMS](http://sms.envaya.org/) webpage:

> EnvayaSMS is a SMS and MMS gateway running entirely as an Android app. It forwards incoming SMS and MMS messages to a web server on the Internet, and sends outgoing messages from the web server to other phones. People with any type of mobile phone can send SMS and MMS to a phone running EnvayaSMS, without needing their own Android phone.

This project is an attempt to make a simple server side part of the system, by utilizing `Redis` and simple HTTP(S). EnvayaSMS can be set up to "check in" every minute to see if anything needs to be broadcasted.

The system supports multiple phones and and relies heavily on Redis for message queuing (both sending and receiving).

```text
Phone (EnvayaSMS app) -> HTTP(S) -> Envaya-SMS-API (PHP) -> Redis
```

Install
-------

Just use composer:

```bash
composer install
```

Setup
-----

`app/settings.php`
```php
return [
    'settings' => [
        'displayErrorDetails' => (ENV == 'development'),

        'envaya_sms' => [
            'password' => '<PASSWORD>',
        ],

        'api' => [
            'tokens' => [
                '<API TOKEN>',
            ]
        ],

        'logger' => [
            'name' => 'sms-service',
            'path' => APP_ROOT . '/logs/app.log',
        ],
    ],
];
```

Where `<PASSWORD>` should be replaced with the password you used in the EnvayaSMS app on the phone. And `<API TOKEN>` is a token that you use when you comminicate with the API.

Usage
-----

```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_URL, sprintf('https://blamh.com/sms/api/%s/message', urlencode('<PHONE>')));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic <API TOKEN>']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['text' => 'SMS text for recipient', 'recipient' => '<RECIPIENT PHONE>']);
ob_start();
curl_exec($ch);
$r = ob_get_clean();
curl_close($ch);

print $r . "\n";
```

Where `<PHONE>` is the phone number for the sender phone, `<API TOKEN>` is the token that you use when communicating with the API and `<RECIPIENT PHONE>` is the phone numer for the recipient. Note that you should prefix all the numbers with the country code. Like `+4525115680`.

The result looks like this:
```json
{
	"status" : "ok",
    "id" : "af8a7b06e531161c",
    "data" : {
    	"to" : "+4525115680",
        "message" : "SMS text for recipient"
    }
}
```

The ID is a unique ID used for keeping track of the message in the queues and is also used by EnvayaSMS to make sure messages are not sent twice.
