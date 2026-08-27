<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi\Tests\Unit;

use Aldytoi\LaravelToi\Tests\TestCase;
use Illuminate\Support\Facades\File;

class InstallCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanTestFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanTestFiles();
        parent::tearDown();
    }

    public function test_installer_command_exists(): void
    {
        $this->artisan('toi:install --help')
            ->assertExitCode(0);
    }

    public function test_installer_succeeds(): void
    {
        $this->artisan('toi:install')
            ->assertExitCode(0);
    }

    public function test_installer_creates_user_model(): void
    {
        $this->artisan('toi:install');
        $this->assertFileExists(app_path('Models/User.php'));
    }

    public function test_installer_creates_controllers(): void
    {
        $this->artisan('toi:install');
        $this->assertFileExists(app_path('Http/Controllers/Controller.php'));
        $this->assertFileExists(app_path('Http/Controllers/Auth/LoginController.php'));
        $this->assertFileExists(app_path('Http/Controllers/Auth/RegisterController.php'));
        $this->assertFileExists(app_path('Http/Controllers/Auth/LogoutController.php'));
    }

    public function test_installer_creates_views(): void
    {
        $this->artisan('toi:install');
        $this->assertFileExists(resource_path('views/layouts/app.blade.php'));
        $this->assertFileExists(resource_path('views/layouts/auth.blade.php'));
        $this->assertFileExists(resource_path('views/auth/login.blade.php'));
        $this->assertFileExists(resource_path('views/auth/register.blade.php'));
        $this->assertFileExists(resource_path('views/dashboard/index.blade.php'));
    }

    public function test_installer_creates_routes(): void
    {
        $this->artisan('toi:install');
        $this->assertFileExists(base_path('routes/web.php'));
    }

    public function test_installer_creates_config(): void
    {
        $this->artisan('toi:install');
        $this->assertFileExists(config_path('toi.php'));
    }

    public function test_installer_creates_migration(): void
    {
        $this->artisan('toi:install');
        $migrations = glob(database_path('migrations/*_create_users_table.php'));
        $this->assertNotEmpty($migrations);
    }

    public function test_installer_does_not_overwrite_existing_files(): void
    {
        // Create a marker file
        $userModel = app_path('Models/User.php');
        File::ensureDirectoryExists(dirname($userModel));
        $originalContent = '<?php // ORIGINAL FILE';
        File::put($userModel, $originalContent);

        $this->artisan('toi:install');

        // File should NOT be overwritten
        $this->assertStringContainsString('ORIGINAL FILE', File::get($userModel));
    }

    public function test_installer_force_overwrites_files(): void
    {
        $userModel = app_path('Models/User.php');
        File::ensureDirectoryExists(dirname($userModel));
        $originalContent = '<?php // ORIGINAL FILE';
        File::put($userModel, $originalContent);

        $this->artisan('toi:install', ['--force' => true]);

        // File SHOULD be overwritten
        $this->assertStringNotContainsString('ORIGINAL FILE', File::get($userModel));
        $this->assertStringContainsString('class User', File::get($userModel));
    }

    public function test_installer_is_idempotent(): void
    {
        $this->artisan('toi:install');
        $this->artisan('toi:install');

        // Should not create duplicate files or errors
        $this->assertFileExists(app_path('Models/User.php'));
        $migrations = glob(database_path('migrations/*_create_users_table.php'));
        $this->assertCount(1, $migrations);
    }

    private function cleanTestFiles(): void
    {
        $files = [
            app_path('Models/User.php'),
            app_path('Http/Controllers/Controller.php'),
            app_path('Http/Controllers/Auth/LoginController.php'),
            app_path('Http/Controllers/Auth/RegisterController.php'),
            app_path('Http/Controllers/Auth/LogoutController.php'),
            resource_path('views/layouts/app.blade.php'),
            resource_path('views/layouts/auth.blade.php'),
            resource_path('views/auth/login.blade.php'),
            resource_path('views/auth/register.blade.php'),
            resource_path('views/dashboard/index.blade.php'),
            base_path('routes/web.php'),
            config_path('toi.php'),
            base_path('resources/css/app.css'),
            base_path('resources/js/app.js'),
        ];

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        // Clean up migration files
        $migrations = glob(database_path('migrations/*_create_users_table.php'));
        foreach ($migrations as $migration) {
            File::delete($migration);
        }

        // Clean up empty directories
        $dirs = [
            app_path('Http/Controllers/Auth'),
            resource_path('views/layouts'),
            resource_path('views/auth'),
            resource_path('views/dashboard'),
        ];
        foreach ($dirs as $dir) {
            if (File::isDirectory($dir) && empty(File::allFiles($dir))) {
                File::deleteDirectory($dir);
            }
        }
    }
}
