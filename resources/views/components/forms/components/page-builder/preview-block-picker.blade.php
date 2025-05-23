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
    :width="1200"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <x-slot name="trigger">
        <div class="flex justify-center w-full">
            {{ $trigger }}
        </div>
    </x-slot>

    <div>
        @foreach ($blocks as $block)
            @php
                $wireClickActionArguments = ['block' => $block->getName()];

                if ($afterItem) {
                    $wireClickActionArguments['afterItem'] = $afterItem;
                }

                $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";

            @endphp

            <div style="width:800px; border:1px solid #e5e5e8; height: 450px; overflow:hidden; margin-bottom:5px;">
                <iframe loading="lazy"  src="{{route('filament.block.preview',$block->getName())}}"
                        scrolling="no"
                        frameborder="0" style="
      width: 1920px;
      height: 1080px;
      transform: scale(0.4167);
      transform-origin: top left;
      border: none;
    "></iframe>

            </div>
            <div>
                <button
                    style="margin-bottom: 20px"
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
            </div>


            <hr>

        @endforeach
    </div>
</x-filament::modal>

