<?php

namespace Z3d0X\FilamentFabricator\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePageBlockCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $signature = 'make:filament-fabricator-page-block {name?} {--F|force}';

    protected $description = 'Create a new filament-fabricator page block';

    public function handle(): int
    {
        $pageBlock = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `HeroBlock`)', 'name'))
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
            ->prepend('components\\filament-fabricator\\page-blocks\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($pageBlock)
                ->prepend('Filament\\Fabricator\\Blocks\\')
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
            'namespace' => 'App\\Filament\\Fabricator\\Blocks' . ($pageBlockNamespace !== '' ? "\\{$pageBlockNamespace}" : ''),
            'shortName' => $shortName,
        ]);

        $this->copyStubToApp('PageBlockView', $viewPath);

        $this->info("Successfully created {$pageBlock}!");

        return static::SUCCESS;
    }
}
