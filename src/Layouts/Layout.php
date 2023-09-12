<?php

namespace Z3d0X\FilamentFabricator\Layouts;

use Illuminate\Support\Str;

abstract class Layout
{
    protected static ?string $component;

    protected static ?string $name;

    public static function getName(): string
    {
        return static::$name;
    }

    public static function getLabel(): string
    {
        return Str::headline(static::$name);
    }

    public static function getComponent(): string
    {
        if (isset(static::$component)) {
            return static::$component;
        }

        return 'filament-fabricator.layouts.' . static::getName();
    }
}
