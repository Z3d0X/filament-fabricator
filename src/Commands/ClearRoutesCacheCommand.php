<?php

namespace Z3d0X\FilamentFabricator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class ClearRoutesCacheCommand extends Command
{
    protected $signature = 'filament-fabricator:clear-routes-cache {--R|refresh}';

    protected $description = 'Clear the routes\' cache';

    public function handle(): int
    {
        $shouldRefresh = $this->option('refresh');

        /**
         * @var PageContract[] $pages
         */
        $pages = FilamentFabricator::getPageModel()::query()
            ->whereNull('parent_id')
            ->with('allChildren')
            ->get();

        foreach ($pages as $page) {
            $this->clearPageCache($page, $shouldRefresh);

        }

        return static::SUCCESS;
    }

    protected function clearPageCache(PageContract $page, bool $shouldRefresh = false)
    {
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

        if (! empty($childPages)) {
            foreach ($childPages as $childPage) {
                $this->clearPageCache($childPage, $shouldRefresh);
            }
        }
    }
}
