<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Z3d0X\FilamentFabricator\Commands\ClearRoutesCacheCommand;
use Z3d0X\FilamentFabricator\Models\Page;
use Z3d0X\FilamentFabricator\Services\PageRoutesService;

use function Pest\Laravel\artisan;

describe(ClearRoutesCacheCommand::class, function () {
    beforeEach(function () {
        Config::set('filament-fabricator.routing.prefix', null);
    });

    it('can be resolved through the container', function () {
        $command = resolve(ClearRoutesCacheCommand::class);

        expect($command)->toBeInstanceOf(ClearRoutesCacheCommand::class);
    });

    it('clears all route caches', function () {
        /**
         * @var PageRoutesService $service
         */
        $service = resolve(PageRoutesService::class);

        /**
         * @var Page $page
         */
        $page = Page::create([
            'title' => 'My title',
            'slug' => 'my-slug',
            'blocks' => [],
            'parent_id' => null,
        ]);

        /**
         * @var Page $child
         */
        $child = Page::create([
            'title' => 'My child page',
            'slug' => 'my-child-page',
            'blocks' => [],
            'parent_id' => $page->id,
        ]);

        $service->getAllUrls(); // ensure everything is cached beforehand

        artisan('filament-fabricator:clear-routes-cache')
            ->assertSuccessful();

        expect(Cache::get('filament-fabricator::PageRoutesService::uri-to-id'))->toBeEmpty();
        expect(Cache::get('filament-fabricator::PageRoutesService::id-to-uri'))->toBeEmpty();

        $cacheKeys = [...$page->getAllUrlCacheKeys(), ...$child->getAllUrlCacheKeys()];

        expect($cacheKeys)->not->toBeEmpty();

        expect(
            collect($cacheKeys)
                ->every(fn (string $cacheKey) => ! Cache::has($cacheKey))
        )->toBeTrue();
    });

    it('refreshes the cache properly', function (string $flag, string $newPrefix) {
        /**
         * @var PageRoutesService $service
         */
        $service = resolve(PageRoutesService::class);

        /**
         * @var Page $page
         */
        $page = Page::create([
            'title' => 'My title',
            'slug' => 'my-slug',
            'blocks' => [],
            'parent_id' => null,
        ]);

        /**
         * @var Page $child
         */
        $child = Page::create([
            'title' => 'My child page',
            'slug' => 'my-child-page',
            'blocks' => [],
            'parent_id' => $page->id,
        ]);

        $urls = collect([...$page->getAllUrls(), ...$child->getAllUrls()])->sort()->toArray();

        $prevUTI = Cache::get('filament-fabricator::PageRoutesService::uri-to-id');
        $prevUTI = collect($prevUTI)->sort()->toArray();

        $prevITU = Cache::get('filament-fabricator::PageRoutesService::id-to-uri');
        $prevITU = collect($prevITU)->sort()->toArray();

        Config::set('filament-fabricator.routing.prefix', $newPrefix);

        artisan('filament-fabricator:clear-routes-cache', [
            $flag => true,
        ])
            ->assertSuccessful();

        $newUrls = collect([...$page->getAllUrls(), ...$child->getAllUrls()])->sort()->toArray();
        expect($newUrls)->not->toEqualCanonicalizing($urls);
        expect($newUrls)->not->toBeEmpty();
        expect(
            collect($newUrls)
                ->every(fn (string $url) => str_starts_with($url, "/$newPrefix"))
        )->toBeTrue();

        $newUTI = Cache::get('filament-fabricator::PageRoutesService::uri-to-id');
        $newUTI = collect($newUTI)->sort()->toArray();
        expect($newUTI)->not->toEqual($prevUTI);
        expect($newUTI)->not->toBeEmpty();
        expect(
            collect($newUTI)
                ->keys()
                ->every(fn (string $uri) => str_starts_with($uri, "/$newPrefix"))
        );

        $newITU = Cache::get('filament-fabricator::PageRoutesService::id-to-uri');
        $newITU = collect($newITU)->sort()->toArray();
        expect($newITU)->not->toEqual($prevITU);
        expect($newITU)->not->toBeEmpty();
        expect(
            collect($newITU)
                ->values()
                ->flatten()
                ->every(fn (string $uri) => str_starts_with($uri, "/$newPrefix"))
        );
    })->with([
        ['--refresh', 'newprefix'],
        ['-R', 'np'],
    ]);
});
