<?php

namespace Z3d0X\FilamentFabricator\Commands\Aliases;

use Z3d0X\FilamentFabricator\Commands;

/**
 * @deprecated
 * @see Commands\MakeLayoutCommand
 */
class MakeLayoutCommand extends Commands\MakeLayoutCommand
{
    protected $hidden = true;

    protected $signature = 'make:filament-fabricator-layout {name?} {--F|force}';
}
