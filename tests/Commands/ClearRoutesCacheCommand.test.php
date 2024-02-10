<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Z3d0X\FilamentFabricator\Commands\ClearRoutesCacheCommand;
use Z3d0X\FilamentFabricator\Models\Page;

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

        $urls = [...$page->getAllUrls(), ...$child->getAllUrls()];

        $prevUTI = Cache::get('filament-fabricator::PageRoutesService::uri-to-id');
        $prevITU = Cache::get('filament-fabricator::PageRoutesService::id-to-uri');

        Config::set('filament-fabricator.routing.prefix', $newPrefix);

        artisan('filament-fabricator:clear-routes-cache', [
            $flag => true,
        ])
            ->assertSuccessful();

        $newUrls = [...$page->getAllUrls(), ...$child->getAllUrls()];

        expect($newUrls)->not->toEqualCanonicalizing($urls);

        expect($newUrls)->not->toBeEmpty();

        expect(
            collect($newUrls)
                ->every(fn (string $url) => str_starts_with($url, "/$newPrefix"))
        )->toBeTrue();

        $newUTI = Cache::get('filament-fabricator::PageRoutesService::uri-to-id');

        expect($newUTI)->not->toEqualCanonicalizing($prevUTI);
        expect(
            collect($newUTI)
                ->keys()
                ->every(fn(string $uri) => str_starts_with($uri, "/$newPrefix"))
        );

        $newITU = Cache::get('filament-fabricator::PageRoutesService::id-to-uri');

        expect($newITU)->not->toEqualCanonicalizing($prevITU);
        expect(
            collect($newITU)
                ->values()
                ->flatten()
                ->every(fn(string $uri) => str_starts_with($uri, "/$newPrefix"))
        );
    })->with([
        ['--refresh', 'newprefix'],
        ['-R', 'np'],
    ]);
});
