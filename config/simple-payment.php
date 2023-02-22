<?php

// config for Selmonal/LaravelSimplePayment
return [
    'qpay' => [
        'env' => env('QPAY_ENV', 'fake'),
        'username' => env('QPAY_USERNAME'),
        'password' => env('QPAY_PASSWORD'),
        'invoice_code' => env('QPAY_INVOICE_CODE'),
    ],
];
