<?php

namespace Z3d0X\FilamentFabricator\Http\Controllers;

use Illuminate\Support\Facades\Blade;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page;

class PageController
{
    public function __invoke(?Page $filamentFabricatorPage = null): string
    {
        // Handle root (home) page
        if (blank($filamentFabricatorPage)) {
            $pageUrls = FilamentFabricator::getPageUrls();

            $pageId = array_search('/', $pageUrls);

            /** @var Page $filamentFabricatorPage */
            $filamentFabricatorPage = FilamentFabricator::getPageModel()::query()
                ->where('id', $pageId)
                ->firstOrFail();
        }

        /** @var ?class-string<Layout> $layout */
        // $layout = FilamentFabricator::getLayoutFromName($filamentFabricatorPage?->layout);
        $layout = FilamentFabricator::getLayoutFromName('default');

        if (! isset($layout)) {
            throw new \Exception("Filament Fabricator: Layout \"{$filamentFabricatorPage->layout}\" not found");
        }

        /** @var string $component */
        $component = $layout::getComponent();

        return Blade::render(
            <<<'BLADE'
            <x-dynamic-component
                :component="$component"
                :page="$page"
            />
            BLADE,
            ['component' => $component, 'page' => $filamentFabricatorPage]
        );
    }
}
