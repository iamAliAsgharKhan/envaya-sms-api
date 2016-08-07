<?php

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
