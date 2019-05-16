<?php

namespace Modules\Frontend\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class FrontendAuth
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if (!Auth::guard('users')->check()) {
            return redirect()->intended('/no-permission');
        }
        return $next($request);
    }
}