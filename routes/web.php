<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Page;

Route::middleware(SubstituteBindings::class)->group(function () {
    Route::get('/{page}', function (Page $page) {
        $component = FilamentFabricator::getComponentFromLayoutName($page->layout);

        return Blade::render(
            <<<'BLADE'
            <x-dynamic-component
                :component="$component"
                :page="$page"
            />
            BLADE,
            ['component' => $component, 'page' => $page]
        );
    });
});
