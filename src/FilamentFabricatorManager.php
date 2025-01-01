<?php

namespace Z3d0X\FilamentFabricator;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\Models\Page;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Z3d0X\FilamentFabricator\Services\PageRoutesService;

class FilamentFabricatorManager
{
    const ID = 'filament-fabricator';

    /** @var Collection<string,string> */
    protected Collection $pageBlocks;

    /** @var Collection<string,string> */
    protected Collection $layouts;

    protected array $schemaSlot = [];

    protected array $meta = [];

    protected array $scripts = [];

    protected array $styles = [];

    protected ?string $favicon = 'favicon.ico';

    protected array $pageUrls = [];

    /**
     * @note It's only separated to not cause a major version change.
     * In the next major release, feel free to make it a constructor promoted property
     */
    protected PageRoutesService $routesService;

    public function __construct(?PageRoutesService $routesService = null)
    {
        $this->routesService = $routesService ?? resolve(PageRoutesService::class);

        /** @var Collection<string,string> */
        $pageBlocks = collect([]);

        /** @var Collection<string,string> */
        $layouts = collect([]);

        $this->pageBlocks = $pageBlocks;
        $this->layouts = $layouts;
    }

    /**
     * @param  class-string  $class
     * @param  class-string  $baseClass
     */
    public function registerComponent(string $class, string $baseClass): void
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

    public function registerSchemaSlot(string $name, array|Closure $schema): void
    {
        $this->schemaSlot[$name] = $schema;
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

    public function getLayoutFromName(string $layoutName): ?string
    {
        return $this->layouts->get($layoutName);
    }

    public function getPageBlockFromName(string $name): ?string
    {
        return $this->pageBlocks->get($name);
    }

    public function getLayouts(): array
    {
        return $this->layouts->map(fn ($layout) => $layout::getLabel())->toArray();
    }

    public function getDefaultLayoutName(): ?string
    {
        return $this->layouts->keys()->first();
    }

    public function getPageBlocks(): array
    {
        return $this->pageBlocks->map(fn ($block) => $block::getBlockSchema())->toArray();
    }

    public function getPageBlocksRaw(): array
    {
        return $this->pageBlocks->toArray();
    }

    public function getSchemaSlot(string $name): array|Closure
    {
        return $this->schemaSlot[$name] ?? [];
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

    /** @return class-string<PageContract> */
    public function getPageModel(): string
    {
        return config('filament-fabricator.page-model') ?? Page::class;
    }

    public function getRoutingPrefix(): ?string
    {
        $prefix = config('filament-fabricator.routing.prefix');

        if (is_null($prefix)) {
            return null;
        }

        $prefix = Str::start($prefix, '/');

        if ($prefix === '/') {
            return $prefix;
        }

        return rtrim($prefix, '/');
    }

    public function getPageUrls(): array
    {
        return $this->routesService->getAllUrls();
    }

    public function getPageUrlFromId(int|string $id, bool $prefixSlash = false, array $args = []): ?string
    {
        /** @var (PageContract&Model)|null $page */
        $page = $this->getPageModel()::query()->find($id);

        return $page?->getUrl($args);
    }
}
