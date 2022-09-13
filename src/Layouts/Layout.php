<?php

namespace  Z3d0X\FilamentFabricator\Layouts;

use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

abstract class Layout
{
    protected static ?string $component;

    protected static ?string $name;

    final public function __construct()
    {
        //
    }

    public function getName(): string
    {
        return static::$name;
    }

    public function getLabel(): string
    {
        return Str::headline(static::$name);
    }

    public function getComponent(): string
    {
        if (isset(static::$component)) {
            return static::$component;
        }

        return 'filament-fabricator.layouts.' . $this->getName();
    }

    public static function register(): void
    {
        FilamentFabricator::registerLayout((new static()));
    }
}
