<?php

namespace Z3d0X\FilamentFabricator\Layouts;

use Illuminate\Support\Str;

abstract class Layout
{
    protected static ?string $component = null;

    protected static ?string $name = null;

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
        return static::$component ?? ('filament-fabricator.layouts.' . static::getName());
    }
}
