<?php

namespace Z3d0X\FilamentFabricator;

use Illuminate\Support\Collection;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Page;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class FilamentFabricatorManager
{
    /** @var Collection<int, string> */
    protected Collection $pageBlocks;

    /** @var Collection<int, string> */
    protected Collection $layouts;

    protected array $meta = [];

    protected array $scripts = [];

    protected array $styles = [];

    protected ?string $favicon = 'favicon.ico';

    public function __construct()
    {
        $this->pageBlocks = collect();
        $this->layouts = collect();
    }

    /**
     *  @param  class-string  $class
     *  @param  class-string  $baseClass
     */
    public function register(string $class, string $baseClass): void
    {
        match ($baseClass) {
            Layout::class => static::registerLayout($class),
            PageBlock::class => static::registerPageBlock($class),
            default => throw new \Exception('Invalid class type'),
        };
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

        $this->pageBlocks->put($pageBlock::getName(), $pageBlock);
    }

    public function pushMeta(array $meta): void
    {
        $this->meta = array_merge($this->meta, $meta);
    }

    public function registerScripts(array $scripts): void
    {
        $this->scripts = array_merge($this->scripts, $scripts);
    }

    public function registerStyles(array $styles): void
    {
        $this->styles = array_merge($this->styles, $styles);
    }

    public function favicon(string $favicon): void
    {
        $this->favicon = $favicon;
    }

    public function getLayoutFromName(string $layoutName): string
    {
        return $this->layouts->get($layoutName);
    }

    public function getPageBlockFromName(string $name): string
    {
        return $this->pageBlocks->get($name);
    }

    public function getLayouts(): array
    {
        return $this->layouts->map(fn ($layout) => $layout::getLabel())->toArray();
    }

    public function getPageBlocks(): array
    {
        return $this->pageBlocks->map(fn ($block) => $block::getBlockSchema())->toArray();
    }

    public function getMeta(): array
    {
        return array_unique($this->meta);
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    /** @return class-string */
    public function getPageModel(): string
    {
        return config('filament-fabricator.page-model') ?? Page::class;
    }
}
