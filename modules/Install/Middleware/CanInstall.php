<?php

namespace Modules\Install\Middleware;

use Closure;

class CanInstall
{
    public function handle($request, Closure $next)
    {
        $fileInstalledExists = file_exists(storage_path('installed'));
        if ($fileInstalledExists) {
            return redirect()->route('install.already_install');
        }
        return $next($request);
    }
}