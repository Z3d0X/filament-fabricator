<?php

use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Http\Controllers\PageController;
use Filament\Http\Middleware\Authenticate;

if (config('filament-fabricator.routing.enabled')) {
    Route::middleware(config('filament-fabricator.middleware') ?? [])
        ->prefix(FilamentFabricator::getRoutingPrefix())
        ->group(function () {
            Route::get('/{filamentFabricatorPage?}', PageController::class)
                ->where('filamentFabricatorPage', '.*')
                ->fallback();
        });
}

Route::middleware(['web',Authenticate::class])
    ->group(function () {
        Route::get('/admin/preview/{blockName}', [PageController::class,'preview'])->name('filament.block.preview');
    });
