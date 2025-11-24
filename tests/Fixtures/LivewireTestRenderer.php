<?php

namespace Z3d0X\FilamentFabricator\Tests\Fixtures;

use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Form;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Component;

use function Livewire\store;

class LivewireTestRenderer
{
    /**
     * Render a Livewire component in memory, including nested Filament fields.
     */
    public static function render(Component $component): string
    {
        // 0. Setup
        if (! store($component)->has('errorBag')) {
            $msgBag = new MessageBag;
            $errors = new ViewErrorBag;
            $errors->put('default', $msgBag);

            /**
             * @var ViewErrorBag $previouslySharedErrors
             */
            $previouslySharedErrors = View::getShared()['errors'] ?? $errors;

            $component->setErrorBag($previouslySharedErrors->getMessages());
            View::share('errors', $previouslySharedErrors);
            session()->put('errors', $previouslySharedErrors);
        }

        // 1. Hydrate public properties (no request needed)
        foreach (get_object_vars($component) as $prop => $value) {
            $component->$prop = $value;
        }

        // 2. Run mount hook if exists
        if (method_exists($component, 'mount')) {
            $component->mount();
        }

        // 3. Resolve the form if it's a HasForms component
        if ($component instanceof HasForms) {
            $form = $component->form($component->form ?? Form::make());
        }

        // 4. Provide $errors to Blade
        $errors = $component->getErrorBag();

        // 5. Render the component's view in memory
        $viewData = [
            'errors' => $errors,
            'form' => $form ?? null,
        ];

        $viewName = property_exists($component, 'view') ? $component->view : null;

        if ($viewName) {
            return view($viewName, $viewData)->render();
        } elseif (method_exists($component, 'render')) {
            return $component->render()->with($viewData)->render();
        }

        // Fallback: if no view, render form directly
        if ($form instanceof Htmlable) {
            return $form->toHtml();
        }

        return '';
    }
}
