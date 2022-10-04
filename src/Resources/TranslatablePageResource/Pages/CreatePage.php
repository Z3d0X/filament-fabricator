<?php

namespace Z3d0X\FilamentFabricator\Resources\TranslatablePageResource\Pages;

use Arr;
use Filament\Pages\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Z3d0X\FilamentFabricator\Resources\TranslatablePageResource;

class CreatePage extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = TranslatablePageResource::class;

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make()
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {

        $record = static::getModel()::usingLocale(
            $this->activeFormLocale,
        )->fill(Arr::except($data, 'blocks'));
        $record->setTranslation('blocks', $this->activeFormLocale, $data['blocks']);
        $record->save();


        return $record;
    }
}
