<?php

namespace Z3d0X\FilamentFabricator\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

class PageRoutesService
{
    protected const URI_TO_ID_MAPPING = 'filament-fabricator::PageRoutesService::uri-to-id';

    protected const ID_TO_URI_MAPPING = 'filament-fabricator::PageRoutesService::id-to-uri';

    /**
     * Get the ID of the Page model to which the given URI is associated, -1 if non matches
     */
    public function getPageIdFromUri(string $uri): int
    {
        $mapping = $this->getUriToIdMapping();
        $uri = Str::start($uri, '/');

        return $mapping[$uri] ?? -1;
    }

    /**
     * Get an instance of your Page model from a URI, or NULL if none matches
     *
     * @return ?Page
     */
    public function getPageFromUri(string $uri): ?Page
    {
        $id = $this->getPageIdFromUri($uri);

        if ($id === -1) {
            return null;
        }

        /** @var Page&Model */
        return FilamentFabricator::getPageModel()::find($id);
    }

    /**
     * Update the cached URLs for the given page (as well as all its descendants')
     */
    public function updateUrlsOf(Page $page): void
    {
        FilamentFabricator::getPageModel()::withoutEvents(function () use ($page) {
            $mapping = $this->getUriToIdMapping();
            $this->updateUrlsAndDescendantsOf($page, $mapping);
            $this->replaceUriToIdMapping($mapping);
        });
    }

    /**
     * Remove the cached URLs for the given page
     */
    public function removeUrlsOf(Page $page): void
    {
        $this->forgetPageLocalCache($page);

        $idToUrlsMapping = $this->getIdToUrisMapping();
        $urls = $idToUrlsMapping[$page->id];
        $idToUrlsMapping[$page->id] = [];
        unset($idToUrlsMapping[$page->id]);
        $this->replaceIdToUriMapping($idToUrlsMapping);

        $uriToIdMapping = $this->getUriToIdMapping();
        foreach ($urls as $uri) {
            unset($uriToIdMapping[$uri]);
        }
        $this->replaceUriToIdMapping($uriToIdMapping);
    }

    /**
     * Get an instance of your Page model from a URI, or throw if there is none
     */
    public function findPageOrFail(string $uri): Page&Model
    {
        $id = $this->getPageIdFromUri($uri);

        /** @var Page&Model */
        return FilamentFabricator::getPageModel()::findOrFail($id);
    }

    /**
     * Get the list of all the registered URLs
     *
     * @return string[]
     */
    public function getAllUrls(): array
    {
        $mapping = $this->getUriToIdMapping();

        return array_values(array_keys($mapping));
    }

    /**
     * Get the URI -> ID mapping
     *
     * @return array<string, int>
     */
    protected function getUriToIdMapping(): array
    {
        return Cache::rememberForever(static::URI_TO_ID_MAPPING, function () {
            $idsToUris = $this->getIdToUrisMapping();
            $mapping = [];

            foreach ($idsToUris as $id => $uris) {
                foreach ($uris as $uri) {
                    $mapping[$uri] = $id;
                }
            }

            return $mapping;
        });
    }

    /**
     * Get the ID -> URI[] mapping
     *
     * @return array<int, string[]>
     */
    protected function getIdToUrisMapping(): array
    {
        return Cache::rememberForever(static::ID_TO_URI_MAPPING, function () {
            $pages = FilamentFabricator::getPageModel()::all();
            $mapping = [];
            $pages->each(function (Page $page) use (&$mapping) {
                $mapping[$page->id] = $page->getAllUrls();
            });

            return $mapping;
        });
    }

    /**
     * Get the cached URIs for the given page
     *
     * @return string[]
     */
    protected function getUrisForPage(Page $page): array
    {
        $mapping = $this->getIdToUrisMapping();

        return $mapping[$page->id] ?? [];
    }

    /**
     * Update routine for the given page
     *
     * @param  array  $mapping  - The URI -> ID mapping (as a reference, to be modified in-place)
     * @return void
     */
    protected function updateUrlsAndDescendantsOf(Page $page, array &$mapping)
    {
        $this->unsetOldUrlsOf($page, $mapping);
        $urls = $page->getAllUrls();

        foreach ($urls as $uri) {
            $id = $mapping[$uri] ?? -1;

            if ($id === $page->id) {
                // Skip if the URI is already mapped to the right ID
                continue;
            }

            unset($mapping[$uri]);
            $mapping[$uri] = $page->id;
        }

        $page->load(['allChildren']);
        foreach ($page->allChildren as $childPage) {
            $this->updateUrlsAndDescendantsOf($childPage, $mapping);
        }
    }

    /**
     * Remove old URLs of the given page from the cached mappings
     *
     * @param  array  $mapping  - The URI -> ID mapping (as a reference, to be modified in-place)
     * @return void
     */
    protected function unsetOldUrlsOf(Page $page, array &$mapping)
    {
        $this->forgetPageLocalCache($page);

        $oldUrlSet = collect($this->getUrisForPage($page))->lazy()->sort()->all();
        $newUrlSet = collect($page->getAllUrls())->lazy()->sort()->all();

        $oldUrls = array_diff($oldUrlSet, $newUrlSet);

        foreach ($oldUrls as $oldUrl) {
            unset($mapping[$oldUrl]);
        }

        $idToUrlsMapping = $this->getIdToUrisMapping();
        $idToUrlsMapping[$page->id] = $newUrlSet;
        $this->replaceIdToUriMapping($idToUrlsMapping);
    }

    protected function forgetPageLocalCache(Page $page)
    {
        $cacheKeys = array_map([$page, 'getUrlCacheKey'], $page->getAllUrlCacheKeysArgs());
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Completely replaced the cached ID -> URI[] mapping
     */
    protected function replaceIdToUriMapping(array $mapping): void
    {
        Cache::forever(static::ID_TO_URI_MAPPING, $mapping);
    }

    /**
     * Completely replace the cached URI -> ID mapping
     */
    protected function replaceUriToIdMapping(array $mapping): void
    {
        Cache::forever(static::URI_TO_ID_MAPPING, $mapping);
    }
}
