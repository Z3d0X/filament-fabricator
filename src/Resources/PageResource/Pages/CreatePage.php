<?php

namespace Z3d0X\FilamentFabricator\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    public static function getResource(): string
    {
        return config('filament-fabricator.page-resource') ?? static::$resource;
    }
}
