<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi;

use Aldytoi\LaravelToi\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;

class LaravelToiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/toi.php' => config_path('toi.php'),
        ], 'toi-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/toi.php',
            'toi'
        );

        $this->commands([
            InstallCommand::class,
        ]);
    }
}
