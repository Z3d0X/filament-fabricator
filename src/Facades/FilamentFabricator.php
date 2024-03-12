<?php

namespace Z3d0X\FilamentFabricator\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

/**
 * @method static void registerComponent(string $class, string $baseClass)
 * @method static void registerLayout(string $layout)
 * @method static void registerPageBlock(string $pageBlock)
 * @method static void registerSchemaSlot(string $name, array | \Closure $schema)
 * @method static void pushMeta(array $meta)
 * @method static void registerScripts(array $scripts)
 * @method static void registerStyles(array $styles)
 * @method static void favicon(?string $favicon)
 * @method static ?string getLayoutFromName(string $layoutName)
 * @method static ?string getPageBlockFromName(string $name)
 * @method static array getLayouts()
 * @method static string getDefaultLayoutName()
 * @method static array getPageBlocks()
 * @method static array getPageBlocksRaw()
 * @method static array | \Closure getSchemaSlot(string $name)
 * @method static array getMeta()
 * @method static array getScripts()
 * @method static array getStyles()
 * @method static ?string getFavicon()
 * @method static class-string<PageContract&Model> getPageModel()
 * @method static ?string getRoutingPrefix()
 * @method static array getPageUrls()
 * @method static ?string getPageUrlFromId(int $id, bool $prefixSlash = false)
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
