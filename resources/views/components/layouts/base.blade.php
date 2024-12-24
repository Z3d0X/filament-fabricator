@props([
    'title' => null,
    'dir' => 'ltr',
])

@use(Z3d0X\FilamentFabricator\View\LayoutRenderHook)

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir }}" class="filament-fabricator">

<head>
    {{ \Filament\Support\Facades\FilamentView::renderHook(LayoutRenderHook::HEAD_START) }}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @foreach (\Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getMeta() as $tag)
        {{ $tag }}
    @endforeach

    @if ($favicon = \Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getFavicon())
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <title>{{ $title ? "{$title} - " : null }} {{ config('app.name') }}</title>


    <style>
        [x-cloak=""],
        [x-cloak="x-cloak"],
        [x-cloak="1"] {
            display: none !important;
        }
    </style>


    @foreach (\Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getStyles() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <link rel="stylesheet" href="{{ $path }}" />
        @endif
    @endforeach

    {{ \Filament\Support\Facades\FilamentView::renderHook(LayoutRenderHook::HEAD_END) }}
</head>

<body class="filament-fabricator-body">
    {{ \Filament\Support\Facades\FilamentView::renderHook(LayoutRenderHook::BODY_START) }}

    {{ $slot }}

    {{ \Filament\Support\Facades\FilamentView::renderHook(LayoutRenderHook::SCRIPTS_START) }}

    @foreach (\Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getScripts() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <script defer src="{{ $path }}"></script>
        @endif
    @endforeach

    @stack('scripts')

    {{ \Filament\Support\Facades\FilamentView::renderHook(LayoutRenderHook::SCRIPTS_END) }}

    {{ \Filament\Support\Facades\FilamentView::renderHook(LayoutRenderHook::BODY_END) }}
</body>

</html>
