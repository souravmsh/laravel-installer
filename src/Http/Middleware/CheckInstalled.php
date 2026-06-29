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
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if installer is enabled
        if (!config('laravel_installer.installer_enabled', true)) {
            return $next($request);
        }

        $installLockFile = storage_path(config('laravel_installer.installed_key_path', 'app/private/key.install'));

        if (!File::exists($installLockFile)) {
            return redirect()->route('installer.welcome');
        }

        return $next($request);
    }
}

