<?php

namespace Z3d0X\FilamentFabricator\Resources\TranslatablePageResource\Pages;

use Arr;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Resources\TranslatablePageResource;

class EditPage extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = TranslatablePageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            LocaleSwitcher::make(),
            Action::make('visit')
                ->label(__('filament-fabricator::page-resource.actions.visit'))
                ->url(fn() => config('filament-fabricator.routing.prefix') . FilamentFabricator::getPageUrlFromId($this->record->id, true))
                ->icon('heroicon-o-external-link')
                ->openUrlInNewTab()
                ->color('success')
                ->visible(config('filament-fabricator.routing.enabled')),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->setLocale($this->activeFormLocale)->fill(Arr::except($data, 'blocks'))->save();
        $record->setTranslation('blocks', $this->activeFormLocale, $data['blocks'])->save();
        return $record;
    }
}
