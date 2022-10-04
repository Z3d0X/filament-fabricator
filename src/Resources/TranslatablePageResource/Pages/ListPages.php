<?php

namespace Z3d0X\FilamentFabricator\Resources\TranslatablePageResource\Pages;

use Filament\Pages\Actions;
use Filament\Pages\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Z3d0X\FilamentFabricator\Resources\TranslatablePageResource;

class ListPages extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = TranslatablePageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            LocaleSwitcher::make()
        ];
    }
}
