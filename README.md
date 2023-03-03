# A simple Qpay implementation for Laravel applications.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/selmonal/simple-payment.svg?style=flat-square)](https://packagist.org/packages/selmonal/simple-payment)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/selmonal/simple-payment/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/selmonal/simple-payment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/selmonal/simple-payment/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/selmonal/simple-payment/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/selmonal/simple-payment.svg?style=flat-square)](https://packagist.org/packages/selmonal/simple-payment)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require selmonal/simple-payment
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="simple-payment-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="simple-payment-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="simple-payment-views"
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

- [Mygmarsuren Sedjav](https://github.com/selmonal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
