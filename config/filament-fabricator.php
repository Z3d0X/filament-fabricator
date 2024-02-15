<?php

// config for Z3d0X/FilamentFabricator
return [
    'routing' => [
        'enabled' => true,
        'prefix' => null, //    /pages
    ],

    'layouts' => [
        'namespace' => 'App\\Filament\\Fabricator\\Layouts',
        'path' => app_path('Filament/Fabricator/Layouts'),
        'register' => [
            //
        ],
    ],

    'page-blocks' => [
        'namespace' => 'App\\Filament\\Fabricator\\PageBlocks',
        'path' => app_path('Filament/Fabricator/PageBlocks'),
        'register' => [
            //
        ],
    ],

    'middleware' => [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        // \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'page-model' => \Z3d0X\FilamentFabricator\Models\Page::class,

    'page-resource' => \Z3d0X\FilamentFabricator\Resources\PageResource::class,

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

    /*
     * This is the name of the table that will be created by the migration and
     * used by the above page-model shipped with this package.
     */
    'table_name' => 'pages',
];
