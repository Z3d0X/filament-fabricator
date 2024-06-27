<?php

namespace Z3d0X\FilamentFabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

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

    /**
     * Hook used to mass-preload related data to reduce the number of DB queries.
     * For instance, to load model objects/data from their IDs
     *
     * @param  (array{
     *     type: string,
     *     data: array,
     * })[]  $blocks  - The array of blocks' data for the given page and the given block type
     */
    public static function preloadRelatedData(Page $page, array &$blocks): void {}
}
