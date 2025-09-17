<?php

namespace Z3d0X\FilamentFabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

abstract class PageBlock
{
    protected static string $name = '';

    protected static ?string $component = null;

    /**
     * Create a minimally pre-configured {@link Block} instance for this {@link PageBlock}
     * @return Block
     */
    protected static function createNewBlock(): Block {
        return Block::make(static::getName());
    }

    /**
     * Get a fully configured {@link Block} instance for this {@link PageBlock}
     * @return Block
     */
    public static function getBlockSchema(): Block {
        return static::defineBlock(
            static::createNewBlock(),
        );
    }

    /**
     * Configure the given {@link Block} instance for this {@link PageBlock}
     * @param $block - The block instance to configure
     */
    abstract public static function defineBlock(Block $block): Block;

    public static function getComponent(): string
    {
        return static::$component ?? ('filament-fabricator.page-blocks.' . static::getName());
    }

    /**
     * The unique identifier name used to refer to this {@link PageBlock} type
     */
    public static function getName(): string {
        return static::$name;
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
     *     data: array<string, mixed>,
     * })[]  $blocks  - The array of blocks' data for the given page and the given block type
     */
    public static function preloadRelatedData(Page $page, array &$blocks): void {}
}
