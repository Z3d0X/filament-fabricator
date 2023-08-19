<?php

namespace Z3d0X\FilamentFabricator;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class FilamentFabricatorPlugin implements Plugin
{
    const ID = 'filament-fabricator';

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            config('filament-fabricator.page-resource') ?? PageResource::class,
        ]);

        if (! $panel->hasPlugin(FilamentPeekPlugin::ID)) {
            //Automatically register FilamentPeekPlugin if it is not already registered
            $panel->plugin(FilamentPeekPlugin::make());
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
