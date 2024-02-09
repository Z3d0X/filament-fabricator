<?php

namespace Z3d0X\FilamentFabricator;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use Z3d0X\FilamentFabricator\Enums\BlockPickerStyle;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class FilamentFabricatorPlugin implements Plugin
{
    const ID = 'filament-fabricator';

    protected BlockPickerStyle|Closure|null $blockPickerStyle = null;

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

    public function blockPickerStyle(BlockPickerStyle|null $style): static
    {
        $this->blockPickerStyle = $style;

        return $this;
    }

    public function getBlockPickerStyle(): BlockPickerStyle|null
    {
        return $this->blockPickerStyle;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
