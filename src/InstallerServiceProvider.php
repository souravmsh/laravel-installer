<?php

namespace Souravmsh\Installer;

use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel_installer.php', 'laravel_installer'
        );

        // Register services
        $this->app->singleton('installer.database', function ($app) {
            return new Services\DatabaseService();
        });

        $this->app->singleton('installer.license', function ($app) {
            return new Services\LicenseService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\InstallerInstallCommand::class,
                Console\Commands\InstallerResetCommand::class,
            ]);

            // Auto-publish config file when package is installed (via package:discover)
            if (!file_exists(config_path('laravel_installer.php'))) {
                @copy(__DIR__.'/../config/laravel_installer.php', config_path('laravel_installer.php'));
            }
        }


        // Check if installer is enabled and app is not installed
        // We use raw file_exists and storage_path to avoid any DB triggers
        $installLockFile = storage_path(config('laravel_installer.installed_key_path', 'app/private/key.install'));
        if (config('laravel_installer.installer_enabled', true) && !file_exists($installLockFile)) {
            if (config('laravel_installer.force_install_redirect', true)) {
                $this->overrideDatabaseConfigs();
            }
        }

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'installer');

        // Register middleware aliases
        $router = $this->app['router'];
        $router->aliasMiddleware('installer.check', Http\Middleware\CheckInstalled::class);
        $router->aliasMiddleware('installer.redirect', Http\Middleware\RedirectIfInstalled::class);

        // Push CheckInstalled into the web group so it runs on every web request.
        // The middleware itself exempts installer-related routes (install/*, installer-assets/*).
        $router->pushMiddlewareToGroup('web', Http\Middleware\CheckInstalled::class);

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/laravel_installer.php' => config_path('laravel_installer.php'),
        ], 'laravel-installer-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/installer'),
        ], 'laravel-installer-views');

        // Publish all
        $this->publishes([
            __DIR__.'/../config/laravel_installer.php' => config_path('laravel_installer.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/installer'),
        ], 'laravel-installer-publish');
    }

    /**
     * Override database-dependent configurations to prevent connection attempts.
     */
    protected function overrideDatabaseConfigs(): void
    {
        config([
            'session.driver' => 'file',
            'cache.default'  => 'array',
            'queue.default'  => 'sync',
        ]);
    }
}
