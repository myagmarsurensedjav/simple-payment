<?php

// config for MyagmarsurenSedjav/SimplePayment
use MyagmarsurenSedjav\SimplePayment\Payment;

return [
    'default' => env('SIMPLE_PAYMENT_DEFAULT', 'qpay'),

    'drivers' => [
        'qpay' => [
            'env' => env('QPAY_ENV', 'fake'),
            'username' => env('QPAY_USERNAME'),
            'password' => env('QPAY_PASSWORD'),
            'invoice_code' => env('QPAY_INVOICE_CODE'),
        ],
        'golomt' => [
            'env' => env('GOLOMT_ENV', 'fake'),
            'access_token' => env('GOLOMT_ACCESS_TOKEN'),
            'hash_key' => env('GOLOMT_HASH_KEY'),
        ],
    ],

    'user_model' => 'App\Models\User',

    'payment_model' => Payment::class,

    'notification_middleware' => [
        // 'api'
    ],

    'return_middlewares' => [
        // 'web'
    ],
];
