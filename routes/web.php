<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Http\Controllers\PageController;

if (config('filament-fabricator.routing.enabled')) {
    Route::middleware(SubstituteBindings::class)
        ->prefix(config('filament-fabricator.routing.prefix', null))
        ->group(function () {
            Route::get('/{filamentFabricatorPage?}', PageController::class)
            ->where('filamentFabricatorPage', '.*')
            ->fallback();
        });
}
