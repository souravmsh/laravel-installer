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
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'installer');

        // Publish views and config
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/installer'),
        ], 'installer-views');

        $this->publishes([
            __DIR__.'/../config/laravel_installer.php' => config_path('laravel_installer.php'),
        ], 'laravel-installer-config');

        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('installer.check', Http\Middleware\CheckInstalled::class);
        $router->aliasMiddleware('installer.redirect', Http\Middleware\RedirectIfInstalled::class);
    }
}
