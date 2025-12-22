<?php

namespace Z3d0X\FilamentFabricator\Tests\Fixtures;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Component;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;

class PageBuilderTestComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected MessageBag $msgBag;

    protected ViewErrorBag $errors;

    public function __construct()
    {
        $this->msgBag = new MessageBag;
        $this->errors = new ViewErrorBag;
        $this->errors->put('default', $this->msgBag);
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            PageBuilder::make('blocks')
                ->blocks([]),
        ])->statePath('data');
    }

    public function getErrorBag()
    {
        return $this->errors->getBag('default');
    }

    public function getViewErrorBag()
    {
        return $this->errors;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function save()
    {
        $this->form->getState();
    }

    public function render()
    {
        return view('filament-fabricator::tests.fixtures.blade-wrapper');
    }
}
