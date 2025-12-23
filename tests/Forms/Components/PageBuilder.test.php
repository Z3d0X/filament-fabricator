<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Z3d0X\FilamentFabricator\Tests\Fixtures\PageBuilderTestComponent;

use function Pest\Livewire\livewire;

describe(PageBuilder::class, function () {
    beforeEach(function () {
        Filament::setCurrentPanel(
            Panel::make()
                ->id('test')
                ->plugins([
                    FilamentFabricatorPlugin::make(),
                ]),
        );
    });

    it('renders without throwing an exception', function () {
        // TODO: Make the test run

        livewire(PageBuilderTestComponent::class)
            ->fillForm([
                'data' => [
                    ['title' => 'Test Item'], // make sure at least one item exists
                ],
            ])
            ->assertSeeHtml('class="fi-fo-builder-item')
            ->assertSchemaExists('blocks');
    })->skip('Proper Livewire unit testing isn\'t possible atm');
});
