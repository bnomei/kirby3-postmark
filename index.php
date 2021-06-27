<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bnomei/postmark', [
    'options' => [
        'apitoken' => null, // or callback
        'trap' => null, // or callback
        'email' => [
            'transport' => [
                'type' => 'smtp',
                'host' => 'smtp.postmarkapp.com',
                'port' => 587,
                'security' => 'tsl',
                'auth' => true,
//                'username' => null, // will default to apikey
//                'password' => null, // will default to apisecret
            ]
        ],
        'cache' => true,
        'expires' => 1, // minutes
    ],
]);

if (!class_exists('Bnomei\Postmark')) {
    require_once __DIR__ . '/classes/Postmark.php';
}

if (!function_exists('postmark')) {
    function postmark()
    {
        return \Bnomei\Postmark::singleton();
    }
}
