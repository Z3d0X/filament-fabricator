<?php

namespace Z3d0X\FilamentFabricator;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentFabricatorServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-fabricator';

    protected array $resources = [
        // CustomResource::class,
    ];

    protected array $pages = [
        // CustomPage::class,
    ];

    protected array $widgets = [
        // CustomWidget::class,
    ];

    protected array $styles = [
        'plugin-filament-fabricator' => __DIR__ . '/../dist/filament-fabricator.css',
    ];

    protected array $scripts = [
        'plugin-filament-fabricator' => __DIR__ . '/../dist/filament-fabricator.js',
    ];

    // protected array $beforeCoreScripts = [
    //     'plugin-filament-fabricator' => __DIR__ . '/../dist/filament-fabricator.js',
    // ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }
}
