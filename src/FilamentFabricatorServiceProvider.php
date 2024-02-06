<?php

namespace Z3d0X\FilamentFabricator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\SplFileInfo;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class FilamentFabricatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name(FilamentFabricatorManager::ID)
            ->hasConfigFile()
            ->hasMigrations(
                'create_pages_table',
                'fix_slug_unique_constraint_on_pages_table',
            )
            ->hasRoute('web')
            ->hasViews()
            ->hasTranslations()
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $installCommand) {
                $installCommand
                    ->startWith(fn (InstallCommand $installCommand) => $installCommand->call('filament:upgrade'))
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('z3d0x/filament-fabricator');
            });
    }

    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeLayoutCommand::class,
            Commands\MakePageBlockCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Z3d0X\\FilamentFabricator\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return array_merge($commands, $aliases);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton('filament-fabricator', function () {
            return new FilamentFabricatorManager();
        });
    }

    public function bootingPackage(): void
    {
        Route::bind('filamentFabricatorPage', function ($value) {
            $pageModel = FilamentFabricator::getPageModel();

            $pageUrls = FilamentFabricator::getPageUrls();

            $value = Str::start($value, '/');

            $pageId = array_search($value, $pageUrls);

            return $pageModel::query()
                ->where('id', $pageId)
                ->firstOrFail();
        });

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
                ->each(fn (string $class) => FilamentFabricator::registerComponent($class, $baseClass))
                ->all(),
        );
    }
}
