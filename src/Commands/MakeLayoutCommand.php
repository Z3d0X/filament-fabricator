<?php

namespace Z3d0X\FilamentFabricator\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeLayoutCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $signature = 'filament-fabricator:layout {name?} {--F|force}';

    protected $description = 'Create a new filament-fabricator layout';

    public function handle(): int
    {
        $layout = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `DefaultLayout`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $layoutClass = (string) Str::of($layout)->afterLast('\\');

        $layoutNamespace = Str::of($layout)->contains('\\') ?
            (string) Str::of($layout)->beforeLast('\\') :
            '';

        $shortName = Str::of($layout)
            ->beforeLast('Layout')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $view = Str::of($layout)
            ->beforeLast('Layout')
            ->prepend('components\\filament-fabricator\\layouts\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($layout)
                ->prepend('Filament\\Fabricator\\Layouts\\')
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

        $this->copyStubToApp('Layout', $path, [
            'class' => $layoutClass,
            'namespace' => 'App\\Filament\\Fabricator\\Layouts' . ($layoutNamespace !== '' ? "\\{$layoutNamespace}" : ''),
            'shortName' => $shortName,
        ]);

        $this->copyStubToApp('LayoutView', $viewPath);

        $this->info("Successfully created {$layout}!");

        return static::SUCCESS;
    }
}
