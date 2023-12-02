<?php

namespace Z3d0X\FilamentFabricator\Models\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait HandlesPageUrls {
    /**
     * Get the default arguments for URL generation
     */
    public function getDefaultUrlCacheArgs(): array {
        return [];
    }

    /**
     * Get the cache key for the URL determined by this entity and the provided arguments
     */
    public function getUrlCacheKey(array $args = []): string {
        // $keyArgs = collect($this->getDefaultUrlArgs())->merge($args)->all();
        $id = $this->id;
        return "filament-fabricator::page-url--$id";
    }

    /**
     * Get the URL determined by this entity and the provided arguments
     */
    public function getUrl(array $args = []): string {
        $cacheKey = $this->getUrlCacheKey($args);

        return Cache::rememberForever($cacheKey, function () use($args) {
            /**
             * @var ?Page $parent
             */
            $parent = $this->parent;
            $parentUri = is_null($parent) ? '/' : $parent->getUrl($args);
            $parentUri = Str::start($parentUri, '/');

            $selfUri = $this->slug;
            $selfUri = Str::start($selfUri, '/');
            return $parentUri === '/' ? $selfUri : "{$parentUri}{$selfUri}";
        });
    }

    /**
     * Get all the available argument sets for the available cache keys
     * @return array[]
     */
    public function getAllCacheKeyArgs(): array {
        return [
            $this->getDefaultUrlCacheArgs(),
        ];
    }

    /**
     * Get all the available URLs for this entity
     * @return string[]
     */
    public function getAllUrls(): array {
        return array_map([$this, 'getUrl'], $this->getAllUrlCacheKeysArgs());
    }
}