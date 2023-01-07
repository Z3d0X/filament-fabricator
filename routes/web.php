<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

if (config('filament-fabricator.routing.enabled')) {
    Route::middleware(SubstituteBindings::class)
        ->prefix(config('filament-fabricator.routing.prefix', null))
        ->group(function () {
            Route::get('/{filamentFabricatorPage?}', function (?Page $filamentFabricatorPage = null) {
                // Handle root (home) page
                if (blank($filamentFabricatorPage)) {
                    $pageUrls = FilamentFabricator::getPageUrls();

                    $pageId = array_search('/', $pageUrls);

                    $filamentFabricatorPage = FilamentFabricator::getPageModel()::query()
                        ->where('id', $pageId)
                        ->firstOrFail();
                }

                $component = FilamentFabricator::getLayoutFromName($filamentFabricatorPage?->layout)::getComponent();

                return Blade::render(
                    <<<'BLADE'
                    <x-dynamic-component
                        :component="$component"
                        :page="$page"
                    />
                    BLADE,
                    ['component' => $component, 'page' => $filamentFabricatorPage]
                );
            })
            ->where('filamentFabricatorPage', '.*')
            ->fallback();
        });
}
