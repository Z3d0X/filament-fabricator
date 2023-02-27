<?php

namespace Z3d0X\FilamentFabricator\Commands\Aliases;

use Z3d0X\FilamentFabricator\Commands;

/**
 * @deprecated
 * @see Commands\MakePageBlockCommand
 */
class MakePageBlockCommand extends Commands\MakePageBlockCommand
{
    protected $hidden = true;

    protected $signature = 'make:filament-fabricator-page-block {name?} {--F|force}';
}
