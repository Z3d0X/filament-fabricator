<?php

namespace  Z3d0X\FilamentFabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;

abstract class PageBlock
{
    protected static ?string $component;

    abstract public static function getBlockSchema(): Block;

    public static function getComponent(): string
    {
        if (isset(static::$component)) {
            return static::$component;
        }

        return 'filament-fabricator.page-blocks.' . static::getName();
    }

    public static function getName(): string
    {
        return static::getBlockSchema()->getName();
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
