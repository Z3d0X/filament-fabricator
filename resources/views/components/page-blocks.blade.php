@aware(['page'])
@props(['blocks' => []])

@php
    $groups = \Z3d0X\FilamentFabricator\Helpers::arrayRefsGroupBy($blocks, 'type');

    foreach ($groups as $blockType => &$group) {
        /**
         * @var class-string<\Z3d0X\FilamentFabricator\PageBlocks\PageBlock> $blockClass
         */
        $blockClass = FilamentFabricator::getPageBlockFromName($blockType);

        if (!empty($blockClass) && $page !== null) {
            $blockClass::preloadRelatedData($page, $group);
        }
    }
@endphp

@foreach ($blocks as $blockData)
    @php
        $pageBlock = \Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageBlockFromName($blockData['type']);
    @endphp

    @isset($pageBlock)
        <x-dynamic-component :component="$pageBlock::getComponent()" :attributes="new \Illuminate\View\ComponentAttributeBag($pageBlock::mutateData($blockData['data']))" />
    @endisset
@endforeach
