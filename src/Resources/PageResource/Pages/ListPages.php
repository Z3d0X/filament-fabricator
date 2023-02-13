<?php

namespace Z3d0X\FilamentFabricator\Resources\PageResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    public static function getResource(): string
    {
        return config('filament-fabricator.page-resource') ?? static::$resource;
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
