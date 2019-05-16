<?php

namespace Modules\Frontend\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class FrontendLocale
{
    public function handle($request, Closure $next)
    {
        if (!Session::has('frontend_locale')) {
            Session::put('frontend_locale', config('app.locale'));
        }
        Lang::setLocale(Session::get('frontend_locale'));
        return $next($request);
    }
}