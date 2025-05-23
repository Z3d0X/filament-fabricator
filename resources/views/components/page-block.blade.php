@aware(['page'])
@props(['block'])



@isset($block)
    <x-dynamic-component :component="$block::getComponent()" :attributes="new \Illuminate\View\ComponentAttributeBag($block::previewData() ?? [])"   />
@endisset
