<?php

namespace Souravmsh\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $installLockFile = storage_path('.installed');

        if (!File::exists($installLockFile)) {
            return redirect()->route('installer.welcome');
        }

        return $next($request);
    }
}
