<?php

namespace Modules\Backend\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BackendLocale
{
    public function handle($request, Closure $next)
    {
        if (!Session::has('backend_locale')) {
            Session::put('backend_locale', config('app.locale'));
        }
        Lang::setLocale(Session::get('backend_locale'));
        return $next($request);
    }
}