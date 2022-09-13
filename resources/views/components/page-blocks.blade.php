@props(['blocks' => []])

@foreach ($blocks as $block)
    <x-dynamic-component
        :component="Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getComponentFromBlockName($block['type'])"
        :attributes="new \Illuminate\View\ComponentAttributeBag($block['data'])"
    />
@endforeach