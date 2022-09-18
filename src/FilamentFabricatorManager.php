<?php

namespace Z3d0X\FilamentFabricator;

use Illuminate\Support\Collection;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class FilamentFabricatorManager
{
    protected Collection $blocks;

    protected Collection $layouts;

    public function __construct()
    {
        $this->blocks = collect();
        $this->layouts = collect();
    }

    /** @param  class-string  $layout */
    public function registerLayout(string $layout): void
    {
        if (! is_subclass_of($layout, Layout::class)) {
            throw new \InvalidArgumentException("{$layout} must extend " . Layout::class);
        }

        $this->layouts->put($layout::getName(), $layout);
    }

    /** @param  class-string  $pageBlock */
    public function registerPageBlock(string $pageBlock): void
    {
        if (! is_subclass_of($pageBlock, PageBlock::class)) {
            throw new \InvalidArgumentException("{$pageBlock} must extend " . PageBlock::class);
        }

        $this->blocks->put($pageBlock::getName(), $pageBlock);
    }

    public function getComponentFromLayoutName(string $layoutName): string
    {
        return $this->layouts->get($layoutName)::getComponent();
    }

    public function getComponentFromBlockName(string $name): string
    {
        return $this->blocks->get($name)::getComponent();
    }

    public function getLayouts(): array
    {
        return $this->layouts->map(fn ($layout) => $layout::getLabel())->toArray();
    }

    public function getBlocks(): array
    {
        return $this->blocks->map(fn ($block) => $block::getBlockSchema())->toArray();
    }
}
