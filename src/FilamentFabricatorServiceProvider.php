<?php

namespace Z3d0X\FilamentFabricator;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
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
    }
}
