<?php

use Inquisition\Foundation\Config\Config;

$config = Config::getInstance();

$config->merge([
    'security' => [
        'secret' => null,
        'refresh_token' => [
            'time_to_live' => '30 days', //https://www.php.net/manual/en/dateinterval.format.php
            'secret' => null,
        ],
        'jwt' => [
            'time_to_live' => '1 day', //https://www.php.net/manual/en/dateinterval.format.php
            'secret' => null,
            'algo' => 'HS256',
        ],
    ]
]);