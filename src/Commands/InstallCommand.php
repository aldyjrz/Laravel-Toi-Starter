<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'toi:install {--force : Overwrite existing files}';

    protected $description = 'Install the Laravel Toi starter kit';

    private Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $this->newLine();
        $this->info('  Laravel Toi Installer');
        $this->newLine();

        if (!$this->checkLaravelVersion()) {
            return self::FAILURE;
        }

        $force = $this->option('force');

        $this->installConfig($force);
        $this->installUserModel($force);
        $this->installMigration($force);
        $this->installBaseController($force);
        $this->installAuthControllers($force);
        $this->installRoutes($force);
        $this->installBladeLayouts($force);
        $this->installAuthViews($force);
        $this->installDashboardView($force);
        $this->installFrontendResources($force);

        $this->newLine();
        $this->info('  <info>Laravel Toi installed successfully.</info>');
        $this->newLine();
        $this->line('  Next steps:');
        $this->line('    1. Run <comment>php artisan migrate</comment> to create database tables.');
        $this->line('    2. Run <comment>npm install && npm run build</comment> to compile assets.');
        $this->line('    3. Visit <comment>/login</comment> or <comment>/register</comment> to get started.');
        $this->newLine();

        return self::SUCCESS;
    }

    private function checkLaravelVersion(): bool
    {
        $version = app()->version();

        if (version_compare($version, '12.0', '<')) {
            $this->error("  Laravel Toi requires Laravel 12 or higher. Current version: {$version}");
            return false;
        }

        $this->line('  <info>✓</info> Checking Laravel version');
        return true;
    }

    private function installConfig(bool $force): void
    {
        $target = config_path('toi.php');
        $this->copyFile(__DIR__ . '/../../config/toi.php', $target, $force, 'Configuration');
    }

    private function installUserModel(bool $force): void
    {
        $target = app_path('Models/User.php');
        $this->copyFile(__DIR__ . '/../../stubs/Models/User.php.stub', $target, $force, 'User model');
    }

    private function installMigration(bool $force): void
    {
        $timestamp = date('Y_m_d_His');
        $target = database_path("migrations/{$timestamp}_create_users_table.php");
        $source = __DIR__ . '/../../stubs/migrations/create_users_table.php.stub';

        // Check if users migration already exists
        if (!$force && $this->hasUsersMigration()) {
            $this->line('  <info>⏭</info> Users migration already exists. Skipping.');
            return;
        }

        $this->copyFile($source, $target, $force, 'User migration');
    }

    private function hasUsersMigration(): bool
    {
        $migrations = glob(database_path('migrations/*_create_users_table.php'));
        return !empty($migrations);
    }

    private function installBaseController(bool $force): void
    {
        $target = app_path('Http/Controllers/Controller.php');
        $this->copyFile(__DIR__ . '/../../stubs/Controllers/Controller.php.stub', $target, $force, 'Base controller');
    }

    private function installAuthControllers(bool $force): void
    {
        $controllers = [
            'LoginController.php',
            'RegisterController.php',
            'LogoutController.php',
        ];

        foreach ($controllers as $controller) {
            $target = app_path("Http/Controllers/Auth/{$controller}");
            $source = __DIR__ . "/../../stubs/Controllers/Auth/{$controller}.stub";
            $this->copyFile($source, $target, $force, "Auth/{$controller}");
        }
    }

    private function installRoutes(bool $force): void
    {
        $target = base_path('routes/web.php');
        $this->copyFile(__DIR__ . '/../../stubs/routes/web.php.stub', $target, $force, 'Web routes');
    }

    private function installBladeLayouts(bool $force): void
    {
        $layouts = [
            'app.blade.php',
            'auth.blade.php',
        ];

        foreach ($layouts as $layout) {
            $target = resource_path("views/layouts/{$layout}");
            $source = __DIR__ . "/../../stubs/views/layouts/{$layout}.stub";
            $this->copyFile($source, $target, $force, "Layout {$layout}");
        }
    }

    private function installAuthViews(bool $force): void
    {
        $views = [
            'login.blade.php',
            'register.blade.php',
        ];

        foreach ($views as $view) {
            $target = resource_path("views/auth/{$view}");
            $source = __DIR__ . "/../../stubs/views/auth/{$view}.stub";
            $this->copyFile($source, $target, $force, "Auth view {$view}");
        }
    }

    private function installDashboardView(bool $force): void
    {
        $target = resource_path('views/dashboard/index.blade.php');
        $source = __DIR__ . '/../../stubs/views/dashboard/index.blade.php.stub';
        $this->copyFile($source, $target, $force, 'Dashboard view');
    }

    private function installFrontendResources(bool $force): void
    {
        $resources = [
            'resources/css/app.css' => __DIR__ . '/../../stubs/resources/css/app.css',
            'resources/js/app.js' => __DIR__ . '/../../stubs/resources/js/app.js',
        ];

        foreach ($resources as $targetPath => $sourcePath) {
            $target = base_path($targetPath);
            $this->copyFile($sourcePath, $target, $force, 'Frontend resource ' . basename($targetPath));
        }
    }

    private function copyFile(string $source, string $target, bool $force, string $label): void
    {
        if ($this->files->exists($target) && !$force) {
            $this->line("  <info>⏭</info> {$label} already exists. Skipping.");
            return;
        }

        $this->files->ensureDirectoryExists(dirname($target));
        $this->files->copy($source, $target);
        $this->line("  <info>✓</info> Installing {$label}");
    }
}
