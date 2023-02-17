<?php

namespace Z3d0X\FilamentFabricator\Forms\Components;

use Filament\Forms\Components\Builder;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

class PageBuilder extends Builder
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->blocks(FilamentFabricator::getPageBlocks());

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            if (! is_array($state)) {
                return array_values([]);
            }

            $registerPageBlockNames = array_keys(FilamentFabricator::getPageBlocksRaw());

            return collect($state)
                ->filter(fn (array $block) => in_array($block['type'], $registerPageBlockNames, true))
                ->values()
                ->toArray();
        });
    }
}
