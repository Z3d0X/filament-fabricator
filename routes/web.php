<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

if (config('filament-fabricator.routing.enabled')) {
    Route::middleware(SubstituteBindings::class)->group(function () {
        Route::get('/{filamentFabricatorPage}', function ($filamentFabricatorPage) {
            $component = FilamentFabricator::getLayoutFromName($filamentFabricatorPage->layout)::getComponent();

            return Blade::render(
                <<<'BLADE'
                <x-dynamic-component
                    :component="$component"
                    :page="$page"
                />
                BLADE,
                ['component' => $component, 'page' => $filamentFabricatorPage]
            );
        });
    });
}
