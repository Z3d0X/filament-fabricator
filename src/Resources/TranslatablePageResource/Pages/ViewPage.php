<?php

namespace Z3d0X\FilamentFabricator\Resources\TranslatablePageResource\Pages;

use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Resources\TranslatablePageResource;

class ViewPage extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;

    protected static string $resource = TranslatablePageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\LocaleSwitcher::make(),
            Action::make('visit')
                ->label(__('filament-fabricator::page-resource.actions.visit'))
                ->url(fn() => config('filament-fabricator.routing.prefix') . FilamentFabricator::getPageUrlFromId($this->record->id, true))
                ->icon('heroicon-o-external-link')
                ->openUrlInNewTab()
                ->color('success')
                ->visible(config('filament-fabricator.routing.enabled')),
        ];
    }
}
