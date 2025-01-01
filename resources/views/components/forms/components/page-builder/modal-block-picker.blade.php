@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'statePath',
    'trigger',
    'width' => null,
])

<x-filament::modal
    :width="$width"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <x-slot name="trigger">
        <div class="flex justify-center w-full">
            {{ $trigger }}
        </div>
    </x-slot>

    <div class="grid gap-4" style="grid-template-columns: repeat({{ $columns['lg'] ?? $columns['default'] ?? 3 }}, minmax(0, 1fr));">
        @foreach ($blocks as $block)
            @php
                $wireClickActionArguments = ['block' => $block->getName()];

                if ($afterItem) {
                    $wireClickActionArguments['afterItem'] = $afterItem;
                }

                $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
            @endphp


            <button
                type="button"
                class="flex flex-col items-center justify-center border border-gray-200 dark:border-white/10 w-full h-full gap-4 whitespace-nowrap rounded-md p-2 text-sm transition-colors duration-75 outline-none hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
                x-on:click="close"
                wire:click="{{ $wireClickAction }}"
            >
                @if ($icon = $block->getIcon())
                    <x-filament::icon
                        :icon="$icon"
                        class="h-8 w-8 text-gray-400 dark:text-gray-500"
                    />
                @endif
                <div>
                    {{ $block->getLabel() }}
                </div>
            </button>
        @endforeach
    </div>
</x-filament::modal>

