<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi\Tests\Unit;

use Aldytoi\LaravelToi\LaravelToiServiceProvider;
use Aldytoi\LaravelToi\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_is_registered(): void
    {
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(LaravelToiServiceProvider::class, $providers);
    }

    public function test_config_is_published(): void
    {
        $config = config('toi');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('name', $config);
        $this->assertArrayHasKey('dashboard_uri', $config);
    }

    public function test_config_has_default_values(): void
    {
        $this->assertEquals('admin', config('toi.dashboard_uri'));
    }

    public function test_toi_install_command_is_registered(): void
    {
        $this->artisan('toi:install --help')
            ->assertExitCode(0);
    }
}
