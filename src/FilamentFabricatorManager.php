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

    public function registerLayout(Layout $layout): void
    {
        $this->layouts->put($layout->getName(), $layout);
    }

    public function registerPageBlock(PageBlock $pageBlock): void
    {
        $this->blocks->put($pageBlock->getName(), $pageBlock);
    }
    
    public function getComponentFromLayoutName(string $layoutName): string
    {
        return $this->layouts->get($layoutName)->getComponent();
    }

    public function getComponentFromBlockName(string $name): string
    {
        return $this->blocks->get($name)->getComponent();
    }

    public function getLayouts(): array
    {
        return $this->layouts->map->getLabel()->toArray();
    }

    public function getBlocks(): array
    {
        return $this->blocks->map->getBlockSchema()->toArray();
    }
}
