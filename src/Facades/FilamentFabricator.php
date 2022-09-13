<?php

namespace Z3d0X\FilamentFabricator\Facades;

use Illuminate\Support\Facades\Facade;

class FilamentFabricator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-fabricator';
    }
}
