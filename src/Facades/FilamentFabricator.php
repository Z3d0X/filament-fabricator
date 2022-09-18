<?php

namespace Z3d0X\FilamentFabricator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $class, string $baseClass)
 * @method static void registerLayout(string $layout)
 * @method static void registerPageBlock(string $pageBlock)
 * @method static string getComponentFromLayoutName(string $layoutName)
 * @method static string getComponentFromBlockName(string $name)
 * @method static array getLayouts()
 * @method static array getPageBlocks()
 *
 * @see \Z3d0X\FilamentFabricator\FilamentFabricatorManager
 */
class FilamentFabricator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-fabricator';
    }
}
