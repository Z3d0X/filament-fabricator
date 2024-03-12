<?php

namespace Z3d0X\FilamentFabricator\Models\Contracts;

interface HasPageUrls
{
    /**
     * Get the default arguments for URL generation
     *
     * @invariant HasPageUrls#getDefaultUrlCacheArgs() must always return the same value, regardless of app/user configuration
     */
    public function getDefaultUrlCacheArgs(): array;

    /**
     * Get the cache key for the URL determined by this entity and the provided arguments
     */
    public function getUrlCacheKey(array $args = []): string;

    /**
     * Get the URL determined by this entity and the provided arguments
     */
    public function getUrl(array $args = []): string;

    /**
     * Get all the available argument sets for the available cache keys
     *
     * @return array[]
     */
    public function getAllUrlCacheKeysArgs(): array;

    /**
     * Get all the available URLs for this entity
     *
     * @return string[]
     */
    public function getAllUrls(): array;

    /**
     * Get all the cache keys for the available URLs for this entity
     *
     * @return string[]
     */
    public function getAllUrlCacheKeys(): array;
}
