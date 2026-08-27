<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi;

use Aldytoi\LaravelToi\Commands\InstallCommand;
use Illuminate\Support\Facades\Blade;
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

        $this->registerLucideComponents();

        $this->commands([
            InstallCommand::class,
        ]);
    }

    private function registerLucideComponents(): void
    {
        $icons = [
            'mail', 'lock', 'eye', 'eye-off', 'log-in',
            'user', 'user-plus', 'user-check', 'user-circle',
            'layout-dashboard', 'users', 'settings',
            'log-out', 'search', 'bell', 'menu',
            'chevron-down', 'check-circle',
        ];

        foreach ($icons as $icon) {
            Blade::component(
                'lucide-' . $icon,
                \Aldytoi\LaravelToi\View\Components\LucideIcon::class
            );
        }
    }
}
