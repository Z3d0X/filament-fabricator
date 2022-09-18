# Block-Based Page Builder Skeleton for your Filament Apps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/z3d0x/filament-fabricator.svg?style=flat-square)](https://packagist.org/packages/z3d0x/filament-fabricator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/z3d0x/filament-fabricator/run-tests?label=tests)](https://github.com/z3d0x/filament-fabricator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/z3d0x/filament-fabricator/Check%20&%20fix%20styling?label=code%20style)](https://github.com/z3d0x/filament-fabricator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/z3d0x/filament-fabricator.svg?style=flat-square)](https://packagist.org/packages/z3d0x/filament-fabricator)

<p align="center">
  <img src="https://user-images.githubusercontent.com/75579178/190926394-daa1b85d-70cc-4730-9a28-cd0c3a0d1230.png" />
</p>

## Installation

You can install the package via composer:

```bash
composer require z3d0x/filament-fabricator
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-fabricator-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-fabricator-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-fabricator-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filament-fabricator = new Z3d0X\FilamentFabricator();
echo $filament-fabricator->echoPhrase('Hello, Z3d0X!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ziyaan Hassan](https://github.com/Z3d0X)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
