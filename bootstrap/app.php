<?php

use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application;
use Z3d0X\FilamentFabricator\FilamentFabricatorServiceProvider;

$basePathLocator = new class
{
    use CreatesApplication;
};

$app = (new Application($basePathLocator::applicationBasePath()))
    ->configure([
        'enables_package_discoveries' => true,
    ])
    ->createApplication();

$app->register(LivewireServiceProvider::class);
$app->register(FilamentServiceProvider::class);
$app->register(SupportServiceProvider::class);
$app->register(FilamentFabricatorServiceProvider::class);

return $app;
