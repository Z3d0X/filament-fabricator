<?php

namespace Z3d0X\FilamentFabricator\Models\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

trait HandlesPageUrls
{
    /**
     * Get the default arguments for URL generation
     */
    public function getDefaultUrlCacheArgs(): array
    {
        return [];
    }

    /**
     * Get the cache key for the URL determined by this entity and the provided arguments
     *
     * @param  array<string, mixed>  $args
     */
    public function getUrlCacheKey(array $args = []): string
    {
        // $keyArgs = collect($this->getDefaultUrlArgs())->merge($args)->all();
        $id = $this->id;

        return "filament-fabricator::page-url--$id";
    }

    /**
     * Get the URL determined by this entity and the provided arguments
     *
     * @param  array<string, mixed>  $args
     */
    public function getUrl(array $args = []): string
    {
        $cacheKey = $this->getUrlCacheKey($args);

        //NOTE: Users must run the command that clears the routes cache if the routing prefix ever changes

        return Cache::rememberForever($cacheKey, function () use ($args) {
            /**
             * @var ?Page $parent
             */
            $parent = $this->parent;

            // If there's no parent page, then the "parent" URI is just the routing prefix.
            $parentUri = is_null($parent) ? (FilamentFabricator::getRoutingPrefix() ?? '/') : $parent->getUrl($args);

            // Every URI in cache has a leading slash, this ensures it's
            // present even if the prefix doesn't have it set explicitly
            $parentUri = Str::start($parentUri, '/');

            // This page's part of the URL (i.e. its URI) is defined as the slug.
            // For the same reasons as above, we need to add a leading slash.
            $selfUri = $this->slug;
            $selfUri = Str::start($selfUri, '/');

            // If the parent URI is the root, then we have nothing to glue on.
            // Therefore the page's URL is simply its URI.
            // This avoids having two consecutive slashes.
            if ($parentUri === '/') {
                return $selfUri;
            }

            // Remove any trailing slash in the parent URI since
            // every URIs we'll use has a leading slash.
            // This avoids having two consecutive slashes.
            $parentUri = rtrim($parentUri, '/');

            return "{$parentUri}{$selfUri}";
        });
    }

    /**
     * Get all the available argument sets for the available cache keys
     *
     * @return array<string, mixed>[]
     */
    public function getAllUrlCacheKeysArgs(): array
    {
        // By default, the entire list of available URL cache keys
        // is simply a list containing the default one since we can't
        // magically infer all the possible state for the library user's customizations.
        return [
            $this->getDefaultUrlCacheArgs(),
        ];
    }

    /**
     * Get all the available URLs for this entity
     *
     * @return string[]
     */
    public function getAllUrls(): array
    {
        return array_map([$this, 'getUrl'], $this->getAllUrlCacheKeysArgs());
    }

    /**
     * Get all the cache keys for the available URLs for this entity
     *
     * @return string[]
     */
    public function getAllUrlCacheKeys(): array
    {
        return array_map([$this, 'getUrlCacheKey'], $this->getAllUrlCacheKeysArgs());
    }
}
