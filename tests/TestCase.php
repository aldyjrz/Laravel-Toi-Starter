<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi\Tests;

use Aldytoi\LaravelToi\LaravelToiServiceProvider;
use Aldytoi\LaravelToi\Tests\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelToiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('auth.guards.web.provider', 'users');

        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    private function setUpDatabase(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            email_verified_at DATETIME NULL,
            password TEXT NOT NULL,
            remember_token TEXT,
            created_at DATETIME,
            updated_at DATETIME
        )');
    }

    protected function registerAuthRoutes(): void
    {
        $this->app['router']->group(['middleware' => 'web'], function () {
            $this->app['router']->get('/', function () {
                return redirect()->route('login');
            });

            $this->app['router']->get('/login', function () {
                return response()->view('auth.login');
            })->name('login');

            $this->app['router']->post('/login', function (\Illuminate\Http\Request $request) {
                $credentials = $request->validate([
                    'email' => ['required', 'email'],
                    'password' => ['required'],
                ]);

                if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
                    $request->session()->regenerate();
                    return redirect()->intended(route('admin.dashboard'));
                }

                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->onlyInput('email');
            });

            $this->app['router']->get('/register', function () {
                return response()->view('auth.register');
            })->name('register');

            $this->app['router']->post('/register', function (\Illuminate\Http\Request $request) {
                $validated = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => ['required', 'confirmed'],
                ]);

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                ]);

                \Illuminate\Support\Facades\Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'));
            });

            $this->app['router']->post('/logout', function (\Illuminate\Http\Request $request) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/');
            })->name('logout')->middleware('auth');

            $this->app['router']->middleware('auth')->prefix('admin')->name('admin.')->group(function () {
                $this->app['router']->get('/', function () {
                    return response('Dashboard OK', 200);
                })->name('dashboard');
            });
        });
    }

    protected function installViewStubs(): void
    {
        $baseDir = __DIR__ . '/../stubs/';

        $views = [
            'views/layouts/app.blade.php.stub' => resource_path('views/layouts/app.blade.php'),
            'views/layouts/auth.blade.php.stub' => resource_path('views/layouts/auth.blade.php'),
            'views/auth/login.blade.php.stub' => resource_path('views/auth/login.blade.php'),
            'views/auth/register.blade.php.stub' => resource_path('views/auth/register.blade.php'),
            'views/dashboard/index.blade.php.stub' => resource_path('views/dashboard/index.blade.php'),
        ];

        foreach ($views as $stub => $target) {
            $source = $baseDir . $stub;
            if (file_exists($source)) {
                File::ensureDirectoryExists(dirname($target));
                $content = File::get($source);
                // Remove @vite directives that fail in test environment
                $content = str_replace("@vite(['resources/css/app.css', 'resources/js/app.js'])", '<!-- vite -->', $content);
                File::put($target, $content);
            }
        }
    }

    protected function removeViewStubs(): void
    {
        $dirs = [
            resource_path('views/layouts'),
            resource_path('views/auth'),
            resource_path('views/dashboard'),
        ];
        foreach ($dirs as $dir) {
            if (File::isDirectory($dir)) {
                File::deleteDirectory($dir);
            }
        }
    }
}
