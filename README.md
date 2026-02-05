# Block-Based Page Builder Skeleton for your Filament Apps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/z3d0x/filament-fabricator.svg?style=for-the-badge)](https://packagist.org/packages/z3d0x/filament-fabricator)
[![Total Downloads](https://img.shields.io/packagist/dt/z3d0x/filament-fabricator.svg?style=for-the-badge)](https://packagist.org/packages/z3d0x/filament-fabricator)

<p align="center">
  <img alt="fabricator banner" src="https://raw.githubusercontent.com/z3d0x/filament-fabricator/2.x/art/banner.jpg" />
</p>

**_What is Filament Fabricator?_** Filament Fabricator is simply said a block-based page builder skeleton. Filament Fabricator takes care of the PageResource & frontend routing, so you can focus on what really matters: your [Layouts](https://filamentphp.com/plugins/z3d0x-fabricator#layouts) & [Page Blocks](https://filamentphp.com/plugins/z3d0x-fabricator#page-blocks).

## Compatibility

| Fabricator                                                   | Filament | PHP  |
| ------------------------------------------------------------ | -------- | ---- |
| [1.x](https://github.com/z3d0x/filament-fabricator/tree/1.x) | ^2.0     | ^8.0 |
| [2.x](https://github.com/z3d0x/filament-fabricator/tree/2.x) | ^3.0     | ^8.1 |
| [3.x](https://github.com/z3d0x/filament-fabricator/tree/3.x) | ^4.0     | ^8.2 |
| [4.x](https://github.com/z3d0x/filament-fabricator/tree/4.x) | ^5.0     | ^8.3 |

## Installation

You can install the package via composer:

```bash
composer require z3d0x/filament-fabricator
```

After that run the install command:

```bash
php artisan filament-fabricator:install
```

Register a `FilamentFabricatorPlugin` instance in your Panel provider:

```php
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;

//..

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFabricatorPlugin::make(),
        ]);
}
```

Then, publish the registered plugin assets:

```
php artisan filament:assets
```

## Documentation

Documentation can be viewed at: https://filamentphp.com/plugins/z3d0x-fabricator

## Screenshots

<img alt="fabricator-index" src="https://raw.githubusercontent.com/z3d0x/filament-fabricator/2.x/art/list-screenshot.png">
<img alt="fabricator-edit-1" src="https://raw.githubusercontent.com/z3d0x/filament-fabricator/2.x/art/edit-screenshot-1.png">
<img alt="fabricator-edit-2" src="https://raw.githubusercontent.com/z3d0x/filament-fabricator/2.x/art/edit-screenshot-2.png">

## Migrate

### From 3.x to 4.x

- Relies on PHP 8.3 as the minimum version

### From 2.x to 3.x

- There is no longer a default value for the `pages.layout` database column
- `FilamentFabricatorManager#getPageUrlFromId` no longer has a `prefixSlash` parameter
- Relies on PHP 8.2 as the minimum version

### From Filament v4 to Filament v5

Following [the upgrade guide from Filament](https://filamentphp.com/docs/5.x/upgrade-guide) should be enough.

In case it isn't, you can run the following commands:

```bash
composer require filament/upgrade:"^5.0" -W --dev

./vendor/bin/filament-v5

# Run the commands output by the upgrade script, they are unique to your app
composer require filament/filament:"^5.0" z3d0x/filament-fabricator:"^5.0" -W --no-update
composer update
```

### From Filament v3 to Filament v4

Following [the upgrade guide from Filament](https://filamentphp.com/docs/4.x/upgrade-guide) should be enough.

In case it isn't, you can run the following commands:

```bash
composer require filament/upgrade:"^4.0" -W --dev

./vendor/bin/filament-v4

# Run the commands output by the upgrade script, they are unique to your app
composer require filament/filament:"^4.0" z3d0x/filament-fabricator:"^4.0" -W --no-update
composer update
```

For more info on breaking changes, please refer to the [CHANGELOG](CHANGELOG.md)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ZedoX](https://github.com/Z3d0X)
- [Voltra](https://github.com/Voltra)
- [Patrick Boivin](https://github.com/pboivin) - Filament Peek
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
