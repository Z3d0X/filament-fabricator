<?php

namespace Z3d0X\FilamentFabricator;

use Filament\PluginServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Symfony\Component\Finder\SplFileInfo;
use Z3d0X\FilamentFabricator\Commands\MakeLayoutCommand;
use Z3d0X\FilamentFabricator\Commands\MakePageBlockCommand;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class FilamentFabricatorServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-fabricator';

    protected array $resources = [
        PageResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasMigration('create_pages_table')
            ->hasRoute('web')
            ->hasViews()
            ->hasCommands([
                MakePageBlockCommand::class,
                MakeLayoutCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $installCommand) {
                $installCommand
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('z3d0x/filament-fabricator');
            });
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-fabricator', function () {
            return new FilamentFabricatorManager();
        });

        $this->app->booting(function () {
            $this->registerComponentsFromDirectory(
                Layout::class,
                config('filament-fabricator.layouts.register'),
                config('filament-fabricator.layouts.path'),
                config('filament-fabricator.layouts.namespace')
            );

            $this->registerComponentsFromDirectory(
                PageBlock::class,
                config('filament-fabricator.page-blocks.register'),
                config('filament-fabricator.page-blocks.path'),
                config('filament-fabricator.page-blocks.namespace')
            );
        });
    }

    protected function registerComponentsFromDirectory(string $baseClass, array $register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! Str::of($directory)->contains('*'))) {
            return;
        }

        $namespace = Str::of($namespace);

        $register = array_merge(
            $register,
            collect($filesystem->allFiles($directory))
                ->map(function (SplFileInfo $file) use ($namespace): string {
                    $variableNamespace = $namespace->contains('*') ? str_ireplace(
                        ['\\' . $namespace->before('*'), $namespace->after('*')],
                        ['', ''],
                        Str::of($file->getPath())
                            ->after(base_path())
                            ->replace(['/'], ['\\']),
                    ) : null;

                    return (string) $namespace
                        ->append('\\', $file->getRelativePathname())
                        ->replace('*', $variableNamespace)
                        ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
                ->each(fn (string $class) => FilamentFabricator::register($class, $baseClass))
                ->all(),
            );
    }
}
