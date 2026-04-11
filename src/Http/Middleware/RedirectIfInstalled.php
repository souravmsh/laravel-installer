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
        $installLockFile = storage_path('.installed');

        if (File::exists($installLockFile)) {
            return redirect('/')->with('error', 'Application is already installed.');
        }

        return $next($request);
    }
}
