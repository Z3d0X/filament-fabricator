<?php

namespace Z3d0X\FilamentFabricator\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

class MakePageBlockCommand extends Command
{
    use CanManipulateFiles;

    protected $signature = 'filament-fabricator:block {name?} {--F|force}';

    protected $description = 'Create a new filament-fabricator page block';

    public function handle(): int
    {
        $pageBlock = (string) Str::of($this->argument('name') ?? text(
            label: 'What is the block name?',
            placeholder: 'HeroBlock',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $pageBlockClass = (string) Str::of($pageBlock)->afterLast('\\');

        $pageBlockNamespace = Str::of($pageBlock)->contains('\\') ?
            (string) Str::of($pageBlock)->beforeLast('\\') :
            '';

        $shortName = Str::of($pageBlock)
            ->beforeLast('Block')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $view = Str::of($pageBlock)
            ->beforeLast('Block')
            ->prepend('components\\filament-fabricator\\page-blocks\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($pageBlock)
                ->prepend('Filament\\Fabricator\\PageBlocks\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        $viewPath = resource_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $files = [$path, $viewPath];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('PageBlock', $path, [
            'class' => $pageBlockClass,
            'namespace' => 'App\\Filament\\Fabricator\\PageBlocks' . ($pageBlockNamespace !== '' ? "\\{$pageBlockNamespace}" : ''),
            'shortName' => $shortName,
        ]);

        $this->copyStubToApp('PageBlockView', $viewPath);

        $this->info("Successfully created {$pageBlock}!");

        return static::SUCCESS;
    }
}
