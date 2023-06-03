# A simple payment implementation for Laravel applications.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/myagmarsurensedjav/simple-payment.svg?style=flat-square)](https://packagist.org/packages/myagmarsurensedjav/simple-payment)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/myagmarsurensedjav/simple-payment/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/myagmarsurensedjav/simple-payment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/myagmarsurensedjav/simple-payment/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/myagmarsurensedjav/simple-payment/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/myagmarsurensedjav/simple-payment.svg?style=flat-square)](https://packagist.org/packages/myagmarsurensedjav/simple-payment)


## Installation

You can install the package via composer:

```bash
composer require myagmarsurensedjav/simple-payment
```

And run the following installer command

```php
php artisan simple-payment:install
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="simple-payment-config"
```

This is the contents of the published config file:

```php
<?php

// config for MyagmarsurenSedjav/SimplePayment
return [
    'default' => env('SIMPLE_PAYMENT_DEFAULT', 'qpay'),

    'gateways' => [
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

    'notification_middleware' => [
        // 'api'
    ],

    'return_middlewares' => [
        // 'web'
    ],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="simple-payment-views"
```
## Usage

`MyagmarsurenSedjav\SimplePayment\Contracts\Payable` interface should be implemented by the model that will be paid. 

```php
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;

Route::get('/invoices/{invoice}/payment', function (Invoice $invoice) {
    return SimplePayment::create($invoice);
});
```

If you need specific gateway, you can use `driver` method.

```php
SimplePayment::driver('socialpay')->create($invoice);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mygmarsuren Sedjav](https://github.com/myagmarsurensedjav)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
