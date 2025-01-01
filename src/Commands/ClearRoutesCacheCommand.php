<?php

namespace Z3d0X\FilamentFabricator\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\Services\PageRoutesService;

class ClearRoutesCacheCommand extends Command
{
    protected $signature = 'filament-fabricator:clear-routes-cache {--R|refresh}';

    protected $description = 'Clear the routes\' cache';

    public function __construct(protected PageRoutesService $pageRoutesService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $shouldRefresh = (bool) $this->option('refresh');

        /**
         * @var array<array-key,PageContract&Model> $pages
         */
        $pages = FilamentFabricator::getPageModel()::query()
            ->whereNull('parent_id')
            ->with('allChildren')
            ->get();

        foreach ($pages as $page) {
            $this->clearPageCache($page, $shouldRefresh);

            if ($shouldRefresh) {
                $this->pageRoutesService->updateUrlsOf($page);
            }
        }

        return static::SUCCESS;
    }

    protected function clearPageCache(PageContract $page, bool $shouldRefresh = false)
    {
        $this->pageRoutesService->removeUrlsOf($page);
        $argSets = $page->getAllUrlCacheKeysArgs();

        foreach ($argSets as $args) {
            $key = $page->getUrlCacheKey($args);
            Cache::forget($key);

            if ($shouldRefresh) {
                // Caches the URL before returning it
                /* $noop = */ $page->getUrl($args);
            }
        }

        $childPages = $page->allChildren;

        if (filled($childPages)) {
            foreach ($childPages as $childPage) {
                $this->clearPageCache($childPage, $shouldRefresh);
            }
        }
    }
}
