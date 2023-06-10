<?php

namespace Z3d0X\FilamentFabricator\Resources\PageResource\Pages;

use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class EditPage extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = PageResource::class;

    public static function getResource(): string
    {
        return config('filament-fabricator.page-resource') ?? static::$resource;
    }

    protected function getActions(): array
    {
        return [
            PreviewAction::make(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Action::make('visit')
                ->label(__('filament-fabricator::page-resource.actions.visit'))
                ->url(fn () => FilamentFabricator::getPageUrlFromId($this->record->id))
                ->icon('heroicon-o-external-link')
                ->openUrlInNewTab()
                ->color('success')
                ->visible(config('filament-fabricator.routing.enabled')),
            Action::make('save')
                ->action('save')
                ->label(__('filament::resources/pages/edit-record.form.actions.save.label')),
        ];
    }

    protected function getPreviewModalView(): ?string
    {
        return 'filament-fabricator::preview';
    }

    protected function getPreviewModalDataRecordKey(): ?string
    {
        return 'page';
    }

    protected function mutatePreviewModalData($data): array
    {
        $layoutName = $this->data['layout'] ?? null;
        if (! isset($layoutName)) {
            return null;
        }

        $layout = FilamentFabricator::getLayoutFromName($layoutName);

        if (! isset($layout)) {
            throw new \Exception("Filament Fabricator: Layout \"{$layoutName}\" not found");
        }

        /** @var string $component */
        $component = $layout::getComponent();

        $data['component'] = $component;
        return $data;
    }
}
