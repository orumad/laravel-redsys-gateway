# Laravel integration for Redsys Payment Gateway (Spain)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/orumad/laravel-redsys-gateway.svg?style=flat-square)](https://packagist.org/packages/orumad/laravel-redsys-gateway)
[![Build Status](https://travis-ci.org/orumad/laravel-redsys-gateway.svg?branch=master)](https://travis-ci.org/orumad/laravel-redsys-gateway)
[![StyleCI](https://github.styleci.io/repos/277463084/shield?branch=master)](https://github.styleci.io/repos/277463084)
[![Quality Score](https://img.shields.io/scrutinizer/g/orumad/laravel-redsys-gateway.svg?style=flat-square)](https://scrutinizer-ci.com/g/orumad/laravel-redsys-gateway)
[![Total Downloads](https://img.shields.io/packagist/dt/orumad/laravel-redsys-gateway.svg?style=flat-square)](https://packagist.org/packages/orumad/laravel-redsys-gateway)

This package allows you to integrate the spanish payment gateway Redsys in your Laravel app. It can manage all the flow (requests / responses) needed to make payments throught the Redsys platform.


## Instalation

You can add the package to your Laravel app as usual:

```bash
composer require orumad/laravel-redsys-gateway
```

The package comes with two tables migrations used to store all the payments data (requests, notifications/responses):

```bash
php artisan vendor:publish --provider="Orumad\LaravelRedsys\LaravelRedsysServiceProvider" --tag="migrations"
```

After that you can create the tables:

```bash
php artisan migrate
```

You can publish the config file:

```bash
php artisan vendor:publish --provider="Orumad\LaravelRedsys\LaravelRedsysServiceProvider" --tag="config"
```


## How to use

_(wip)_

## Testing

You can test as usual:

```bash
vendor/bin/phpunit
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email dev@danielmunoz.io instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
