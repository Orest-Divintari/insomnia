<?php

return [
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET'),
    ],
    'administrators' => [
        'uric@example.com',
    ],
    'database' => [
        'test_name' => 'insomnia_test_mysql',
        'name' => 'insomnia',
    ],

];