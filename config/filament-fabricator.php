<?php

// config for Z3d0X/FilamentFabricator
return [
    'routing' => [
        /**
         * Whether routing should be automatically handled.
         * Disable if you want finer and manual control over how the routing to your pages is done.
         */
        'enabled' => true,

        /**
         * The prefix to use for all pages' routes.
         * Leave to null if you don't want them to have a prefix.
         * A prefix set to '/pages' means that a page of slug 'page-1'
         * would be accessed through `/pages/page-1` if routing is enabled.
         */
        'prefix' => null, //    /pages
    ],

    'layouts' => [
        /**
         * The base namespace for all your
         * filament-fabricator page layouts
         */
        'namespace' => 'App\\Filament\\Fabricator\\Layouts',

        /**
         * The path to your layouts (folder or glob)
         * This is used when auto-registering them
         */
        'path' => app_path('Filament/Fabricator/Layouts'),

        /**
         * A list of layout classes you want to manually register
         * in addition to those that are auto-registered
         */
        'register' => [
            //
        ],
    ],

    'page-blocks' => [
        /**
         * The base namespace for all your filament-fabricator page blocks
         */
        'namespace' => 'App\\Filament\\Fabricator\\PageBlocks',

        /**
         * The path to your blocks (folder or glob)
         * This is used when auto-registering them
         */
        'path' => app_path('Filament/Fabricator/PageBlocks'),

        /**
         * A list of block classes you want to manually register
         * in addition to those that are auto-registered
         */
        'register' => [
            //
        ],
    ],

    /**
     * The middleware(s) to apply to your pages when routing is enabled
     */
    'middleware' => [
        'web',
    ],

    /**
     * The page model to be used by the package.
     * Replace this if you ever extend it
     */
    'page-model' => \Z3d0X\FilamentFabricator\Models\Page::class,

    /**
     * The page filament resource to be used by the package.
     * Replace this if you ever extend it
     */
    'page-resource' => \Z3d0X\FilamentFabricator\Resources\PageResource::class,

    /**
     * Whether you want to have a view page as part of your PageResource
     */
    'enable-view-page' => false,

    /**
     * Whether to hook into artisan's core commands to clear and refresh page route caches along with the rest.
     * Disable for manual control over cache.
     *
     * This is the list of commands that will be hooked into:
     *  - cache:clear        -> clear routes cache
     *  - config:cache       -> refresh routes cache
     *  - config:clear       -> clear routes cache
     *  - optimize           -> refresh routes cache
     *  - optimize:clear     -> clear routes cache
     *  - route:clear        -> clear routes cache
     */
    'hook-to-commands' => true,

    /**
     * This is the name of the table that will be created by the migration and
     * used by the above page-model shipped with this package.
     */
    'table_name' => 'pages',
];
