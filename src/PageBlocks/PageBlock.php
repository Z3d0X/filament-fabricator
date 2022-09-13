<?php

namespace  Z3d0X\FilamentFabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

abstract class PageBlock
{
    protected static ?string $component;

    abstract public function getBlockSchema(): Block;

    public function getComponent(): string
    {
        if (isset(static::$component)) {
            return static::$component;
        }

        return 'filament-fabricator.page-blocks.' . $this->getName();
    }

    public function getName(): string
    {
        return $this->getBlockSchema()->getName();
    }

    public static function register(): void
    {
        FilamentFabricator::registerPageBlock((new static()));
    }
}
