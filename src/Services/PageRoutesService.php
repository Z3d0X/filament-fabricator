<?php

namespace Z3d0X\FilamentFabricator\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

// The service is a coordinator between all route caches
// There are three layers of caches that have to be in sync:
// - PageRoutesService::URI_TO_ID_MAPPING that maps from URI to ID (many-to-one)
// - PageRoutesService::ID_TO_URI_MAPPING that maps from ID to URIS (one-to-many)
// - Page::getUrl (and thus Page::getAllUrl)
//
// Syncinc and consistencies should be handle via the service.
// As such, it's responsible for maintaining internale consistency
// and hiding/encapsulating implementation details.
//
// It relies on the extension points defined by Z3d0X\FilamentFabricator\Models\Contracts\HasPageUrls
class PageRoutesService
{
    protected const URI_TO_ID_MAPPING = 'filament-fabricator::PageRoutesService::uri-to-id';

    protected const ID_TO_URI_MAPPING = 'filament-fabricator::PageRoutesService::id-to-uri';

    /**
     * Get the ID of the Page model to which the given URI is associated, -1 if non matches
     *
     * @return int The page's ID, or -1 on failure
     */
    public function getPageIdFromUri(string $uri): int
    {
        // Query the (URI -> ID) mapping based on the user provided URI.
        // The mapping expect a URI that starts with a /
        // thus we "normalize" the URI by ensuring it starts with one.
        // Not doing so would result in a false negative.
        $mapping = $this->getUriToIdMapping();
        $uri = Str::start($uri, '/');

        return $mapping[$uri] ?? -1;
    }

    /**
     * Get an instance of your Page model from a URI, or NULL if none matches
     *
     * @return null|(Page&Model)
     */
    public function getPageFromUri(string $uri): ?Page
    {
        $id = $this->getPageIdFromUri($uri);

        // We know the getPageIdFromUri uses -1 as a "sentinel" value
        // for when the page is not found, so return null in those cases
        if ($id === -1) {
            return null;
        }

        return FilamentFabricator::getPageModel()::find($id);
    }

    /**
     * Update the cached URLs for the given page (as well as all its descendants')
     */
    public function updateUrlsOf(Page $page): void
    {
        // We mutate the mapping without events to ensure we don't have "concurrent"
        // modifications of the same mapping. This allows us to skip the use of locks
        // in an environment where only unrelated pages can be modified by separate
        // users at the same time, which is a responsibility the library users
        // should enforce themselves.
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
        // First remove the entries from the (ID -> URI) mapping
        $idToUrlsMapping = $this->getIdToUrisMapping();
        $urls = $idToUrlsMapping[$page->id];
        $idToUrlsMapping[$page->id] = null;
        unset($idToUrlsMapping[$page->id]);
        $this->replaceIdToUriMapping($idToUrlsMapping);

        // Then remove the entries from the (URI -> ID) mapping
        $uriToIdMapping = $this->getUriToIdMapping();
        foreach ($urls as $uri) {
            unset($uriToIdMapping[$uri]);
        }
        $this->replaceUriToIdMapping($uriToIdMapping);

        // Finally, clear the page's local caches of its own URL.
        // This means that Page::getAllUrls() and such will now compute
        // fresh values.
        $this->forgetPageLocalCache($page);
    }

    /**
     * Get an instance of your Page model from a URI, or throw if there is none
     */
    public function findPageOrFail(string $uri): Page&Model
    {
        $id = $this->getPageIdFromUri($uri);

        // If the page doesn't exists, we know getPageIdFromUri
        // will return -1. Thus findOrFail will fail as expected.
        return FilamentFabricator::getPageModel()::findOrFail($id);
    }

    /**
     * Get the list of all the registered URLs
     *
     * @return string[]
     */
    public function getAllUrls(): array
    {
        $uriToIdMapping = $this->getUriToIdMapping();

        // $uriToIdMapping is an associative array that maps URIs to IDs.
        // Thus, the list of URLs is the keys of that array.
        // Since PHP handles keys very weirdly when using array_keys,
        // we simply get its array_values to have a truly regular array
        // instead of an associative array where the keys are all numbers
        // but possibly non-sorted.
        return array_values(array_keys($uriToIdMapping));
    }

    /**
     * Get the URI -> ID mapping
     *
     * @return array<string, int>
     */
    protected function getUriToIdMapping(): array
    {
        // The mapping will be cached for most requests.
        // The very first person hitting the cache when it's not readily available
        // will sadly have to recompute the whole thing.
        return Cache::rememberForever(static::URI_TO_ID_MAPPING, function () {
            // Even though we technically have 2 separate caches
            // we want them to not really be independent.
            // Here we ensure our initial state depends on the other
            // cache's initial state.
            $idsToUrisMapping = $this->getIdToUrisMapping();
            $uriToIdMapping = [];

            // We simply "reverse" the one-to-many mapping to a many-to-one
            foreach ($idsToUrisMapping as $id => $uris) {
                foreach ($uris as $uri) {
                    $uriToIdMapping[$uri] = $id;
                }
            }

            return $uriToIdMapping;
        });
    }

    /**
     * Get the ID -> URI[] mapping
     *
     * @return array<int, string[]>
     */
    protected function getIdToUrisMapping(): array
    {
        // The mapping will be cached for most requests.
        // The very first person hitting the cache when it's not readily available
        // will sadly have to recompute the whole thing.
        // This could be a critical section and bottleneck depending on the use cases.
        // Any optimization to this can greatly improve the entire package's performances
        // in one fell swoop.
        return Cache::rememberForever(static::ID_TO_URI_MAPPING, function () {
            $pages = FilamentFabricator::getPageModel()::all();
            $mapping = [];
            $pages->each(function (Page $page) use (&$mapping) {
                // Note that this also has the benefits of computing
                // the page's local caches.
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
     * @param  array  $uriToIdMapping  - The URI -> ID mapping (as a reference, to be modified in-place)
     * @return void
     */
    protected function updateUrlsAndDescendantsOf(Page $page, array &$uriToIdMapping)
    {
        // First ensure consistency by removing any trace of the old URLs
        // for the given page. Whether local or in the URI to ID mapping.
        $this->unsetOldUrlsOf($page, $uriToIdMapping);

        // These URLs will always be fresh since we unset the old ones just above
        $urls = $page->getAllUrls();

        foreach ($urls as $uri) {
            $id = $uriToIdMapping[$uri] ?? -1;

            // If while iterating the fresh URLs we encounter one
            // that is already mapped to the right page ID
            // then there's nothing to do for this URL
            // and thus continue onward with the next one.
            if ($id === $page->id) {
                continue;
            }

            // Otherwise, we have a URI that already exists
            // and is mapped to the wrong ID, or it wasn't
            // in the mapping yet. In both cases we just need
            // to add it to the mapping at the correct spot.
            $uriToIdMapping[$uri] = $page->id;
        }

        // Since we're recursing down the tree, we preload the relationships
        // once, and traverse down the tree. This helps with performances.
        // TODO: Make it work with loadMissing instead of load to reduce the number of useless DB queries
        $page->load(['allChildren']);
        foreach ($page->allChildren as $childPage) {
            // A change in a parent page will always result
            // in a change to its descendant. As such,
            // we need to recompute everything that's
            // a descendant of this page.
            $this->updateUrlsAndDescendantsOf($childPage, $uriToIdMapping);
        }
    }

    /**
     * Remove old URLs of the given page from the cached mappings
     *
     * @param  array  $uriToIdMapping  - The URI -> ID mapping (as a reference, to be modified in-place)
     * @return void
     */
    protected function unsetOldUrlsOf(Page $page, array &$uriToIdMapping)
    {
        // When we're hitting this path, caches haven't been invalidated yet.
        // Thus we don't need to query the mappings to get the old URLs.
        $oldUrlSet = collect($page->getAllUrls())->lazy()->sort()->all();

        // Once we're done collecting the previous URLs, and since we want
        // to unset ALL old URLs for this given page, we might as well
        // forget its local caches here.
        $this->forgetPageLocalCache($page);

        // Since we just forgot the page's local caches, this doesn't
        // return the old set of URLs, but instead computes and caches
        // the new URLs based on the page's currently loaded data.
        $newUrlSet = collect($page->getAllUrls())->lazy()->sort()->all();

        // The old URLs are those that are present in the $oldUrlSet
        // but are not present in $newUrlSet. Hence the use of array_diff
        // whose role is to return exactly that. Also note we sorted the arrays
        // in order to make sure the diff algorithm has every chances to be
        // optimal in performance.
        $oldUrls = array_diff($oldUrlSet, $newUrlSet);

        // Simply go through the list of old URLs and remove them from the mapping.
        // This is one of the reasons we pass it by reference.
        foreach ($oldUrls as $oldUrl) {
            unset($uriToIdMapping[$oldUrl]);
        }

        $idToUrlsMapping = $this->getIdToUrisMapping();
        $idToUrlsMapping[$page->id] = $newUrlSet;
        $this->replaceIdToUriMapping($idToUrlsMapping);
    }

    /**
     * Forget all URL caches tied to the page (cf. Page::getAllUrlCacheKeys)
     */
    protected function forgetPageLocalCache(Page $page)
    {
        // The page local caches are simply those behind the
        // URL cache keys. Compute the keys, forget the caches.
        $cacheKeys = $page->getAllUrlCacheKeys();
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Completely replaced the cached ID -> URI[] mapping
     *
     * @param  array<int, string[]>  $idToUriMapping
     */
    protected function replaceIdToUriMapping(array $idToUriMapping): void
    {
        // Replace the ID -> URI[] mapping with the given one.
        // This is done "atomically" with regards to the cache.
        // Note that concurrent read and writes can result in lost updates.
        // And thus in an invalid state.
        Cache::forever(static::ID_TO_URI_MAPPING, $idToUriMapping);
    }

    /**
     * Completely replace the cached URI -> ID mapping
     *
     * @param  array<string, int>  $uriToIdMapping
     */
    protected function replaceUriToIdMapping(array $uriToIdMapping): void
    {
        // Replace the URI -> ID mapping with the given one.
        // This is done "atomically" with regards to the cache.
        // Note that concurrent read and writes can result in lost updates.
        // And thus in an invalid state.
        Cache::forever(static::URI_TO_ID_MAPPING, $uriToIdMapping);
    }
}
