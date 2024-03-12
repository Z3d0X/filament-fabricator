<?php

namespace Z3d0X\FilamentFabricator\Listeners;

use Illuminate\Console\Command;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Z3d0X\FilamentFabricator\Commands\ClearRoutesCacheCommand;

class OptimizeWithLaravel
{
    const COMMANDS = [
        'cache:clear',
        'config:cache',
        'config:clear',
        'optimize',
        'optimize:clear',
        'route:clear',
    ];

    const REFRESH_COMMANDS = [
        'config:cache',
        'optimize',
    ];

    public function handle(CommandFinished $event): void
    {
        if (! $this->shouldHandleEvent($event)) {
            return;
        }

        if ($this->shouldRefresh($event)) {
            $this->refresh();
        } else {
            $this->clear();
        }
    }

    public function shouldHandleEvent(CommandFinished $event)
    {
        return $event->exitCode === Command::SUCCESS
            && in_array($event->command, static::COMMANDS);
    }

    public function shouldRefresh(CommandFinished $event)
    {
        return in_array($event->command, static::REFRESH_COMMANDS);
    }

    public function refresh()
    {
        $this->callCommand([
            '--refresh' => true,
        ]);
    }

    public function clear()
    {
        $this->callCommand();
    }

    public function callCommand(array $params = [])
    {
        Artisan::call(ClearRoutesCacheCommand::class, $params);
    }
}
