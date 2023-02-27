<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Http\Controllers\PageController;

if (config('filament-fabricator.routing.enabled')) {
    Route::middleware(SubstituteBindings::class)
        ->prefix(FilamentFabricator::getRoutingPrefix())
        ->group(function () {
            Route::get('/{filamentFabricatorPage?}', PageController::class)
            ->where('filamentFabricatorPage', '.*')
            ->fallback();
        });
}
