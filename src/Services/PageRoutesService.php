<?php

namespace Z3d0X\FilamentFabricator\Services;

use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageRoutesService {
    protected const URI_TO_ID_MAPPING = 'filament-fabricator::PageRoutesService::uri-to-id';
    protected const ID_TO_URI_MAPPING = 'filament-fabricator::PageRoutesService::id-to-uri';

    /**
     * Get the ID of the Page model to which the given URI is associated, -1 if non matches
     * @param string $uri
     * @return int
     */
    public function getPageIdFromUri(string $uri): int
    {
        $mapping = $this->getUriToIdMapping();
        $uri = Str::start($uri, '/');
        return $mapping[$uri] ?? -1;
    }

    /**
     * Get an instance of your Page model from a URI, or NULL if none matches
     * @param string $uri
     * @return ?Page
     */
    public function getPageFromUri(string $uri): ?Page
    {
        $id = $this->getPageIdFromUri($uri);
        return FilamentFabricator::getPageModel()::find($id);
    }

    /**
     * Update the cached URLs for the given page (as well as all its descendants')
     * @param Page $page
     */
    public function updateUrlsOf(Page $page): void
    {
        $mapping = $this->getUriToIdMapping();
        $this->updateUrlsAndDescendantsOf($page, $mapping);
        $this->replaceUriToIdMapping($mapping);
    }

    /**
     * Get an instance of your Page model from a URI, or throw if there is none
     * @param string $uri
     * @return Page
     */
    public function findPageOrFail(string $uri): Page {
        $id = $this->getPageIdFromUri($uri);
        return FilamentFabricator::getPageModel()::findOrFail($id);
    }

    /**
     * Get the URI -> ID mapping
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
     * @param Page $page
     * @return string[]
     */
    protected function getUrisForPage(Page $page): array
    {
        $mapping = $this->getIdToUrisMapping();
        return $mapping[$page->id] ?? [];
    }

    /**
     * Update routine for the given page
     * @param Page $page
     * @param array $mapping - The URI -> ID mapping (as a reference, to be modified in-place)
     * @return void
     */
    protected function updateUrlsAndDescendantsOf(Page $page, array &$mapping)
    {
        $this->unsetOldUrlsOf($page, $mapping);
        $urls = $page->getAllUrls();

        foreach ($urls as $uri) {
            $id = $mapping[$uri] ?? -1;

            if ($id === $page->id) {
                continue;
            }

            unset($mapping[$uri]);
            $mapping[$uri] = $page->id;
        }

        // $page->load(['children:id,slug']);
        foreach ($page->children as $childPage) {
            $this->updateUrlsAndDescendantsOf($childPage, $mapping);
        }
    }

    /**
     * Remove old URLs of the given page from the cached mappings
     * @param Page $page
     * @param array $mapping - The URI -> ID mapping (as a reference, to be modified in-place)
     * @return void
     */
    protected function unsetOldUrlsOf(Page $page, array &$mapping)
    {
        $oldUrlSet = collect($this->getUrisForPage($page))->lazy()->sort()->all();
        $allUrlSet = collect($page->getAllUrls())->lazy()->sort()->all();

        $oldUrls = array_diff($oldUrlSet, $allUrlSet);

        foreach ($oldUrls as $oldUrl) {
            unset($mapping[$oldUrl]);
        }

        $idToUrlsMapping = $this->getIdToUrisMapping();
        $idToUrlsMapping[$page->id] = $allUrlSet;
        $this->replaceIdToUriMapping($idToUrlsMapping);
    }

    /**
     * Completely replaced the cached ID -> URI[] mapping
     * @param array $mapping
     * @return void
     */
    protected function replaceIdToUriMapping(array $mapping): void
    {
        Cache::forget(static::ID_TO_URI_MAPPING);
        Cache::rememberForever(static::ID_TO_URI_MAPPING, fn() => $mapping);
    }

    /**
     * Completely replace the cached URI -> ID mapping
     * @param array $mapping
     * @return void
     */
    protected function replaceUriToIdMapping(array $mapping): void
    {
        Cache::forget(static::URI_TO_ID_MAPPING);
        Cache::rememberForever(static::URI_TO_ID_MAPPING, fn() => $mapping);
    }
}
