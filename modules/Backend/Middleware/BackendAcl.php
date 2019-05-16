<?php

namespace Modules\Backend\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\AclResource;
use Modules\Backend\Core\System;

class BackendAcl
{
    protected $auth;

    /**
     * Handle acl
     * Get controller in url and action to have source to check
     * 'allow - 1', 'deny - 0'
     */
    public function handle($request, Closure $next)
    {
        $user = Session::get('admin');
        $permission = json_decode($user['permission'], true);
        $controller = System::getCurrentController();
        $action = Route::getCurrentRoute()->getActionName();
        $actionArray = explode('@', $action);
        $actionName = $actionArray[1];
        $sourceCheck = $controller.'_'.$actionName;
        if (array_key_exists($sourceCheck, $permission)) {
            if ($permission[$sourceCheck] == System::DENY) {
                if ($actionName == 'index') {
                    //return view
                    return redirect()->intended(config('app.admin_url').'/dashboard/deny-acl-view');
                } else {
                    //return json data to display message
                    return redirect()->intended(config('app.admin_url').'/dashboard/deny-acl');
                }
            }
        }
        return $next($request);
    }
}