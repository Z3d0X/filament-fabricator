<?php

namespace Z3d0X\FilamentFabricator\Resources\PageResource\Pages\Concerns;

use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal as BaseHasPreviewModal;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

trait HasPreviewModal
{
    use BaseHasPreviewModal;

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
            return [];
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
