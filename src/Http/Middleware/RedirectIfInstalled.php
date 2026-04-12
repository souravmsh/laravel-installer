<?php

namespace Souravmsh\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RedirectIfInstalled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if installer is enabled
        if (!config('laravel_installer.installer_enabled', true)) {
            abort(404);
        }

        $installLockFile = storage_path(config('laravel_installer.installed_key_path', 'app/private/key.install'));

        if (File::exists($installLockFile) && !$request->routeIs('installer.complete')) {
            return redirect('/')->with('error', 'Application is already installed.');
        }

        return $next($request);
    }
}
