<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

if (config('filament-fabricator.routing.enabled')) {
    Route::middleware(SubstituteBindings::class)->group(function () {
        Route::get('/{filamentFabricatorPage?}', function ($filamentFabricatorPage = null) {
            // Handle root (home) page
            if (blank($filamentFabricatorPage)) {
                $filamentFabricatorPage = FilamentFabricator::getPageModel()::query()
                    ->where('slug', '/')
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
        ->where('filamentFabricatorPage', '.*');
    });
}
