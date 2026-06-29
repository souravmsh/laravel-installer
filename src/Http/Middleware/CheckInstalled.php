<?php

namespace Souravmsh\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * Redirects to the installer when the installation lock file is missing.
     * Installer-related routes are always exempt from this redirect.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('laravel_installer.installer_enabled', true)) {
            return $next($request);
        }

        $installLockFile = storage_path(config('laravel_installer.installed_key_path', 'app/private/key.install'));

        if (!File::exists($installLockFile)) {
            // Installer-related routes always pass through.
            if ($this->isInstallerRoute($request)) {
                return $next($request);
            }

            // If force install is disabled, allow browsing normally
            if (!config('laravel_installer.force_install_redirect', true)) {
                return $next($request);
            }

            return redirect()->route('installer.welcome');
        }

        return $next($request);
    }


    /**
     * Determine if the request targets an installer route.
     */
    protected function isInstallerRoute(Request $request): bool
    {
        // Named route check (works after routing)
        if ($request->routeIs('installer.*')) {
            return true;
        }

        // Path-based fallback
        return $request->is('install', 'install/*', 'installer-assets/*');
    }
}
