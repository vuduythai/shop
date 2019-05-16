<?php

namespace Modules\Backend\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Facades\Dashboard;
use Modules\Backend\Models\Config;
use Modules\Backend\Core\System;

class DashboardController extends Controller
{
    /**
     * index dashboard
     */
    public function index()
    {
        $data = Dashboard::getDashboardData();
        return view('Backend.View::dashboard.index', $data);
    }

    /**
     * Display view deny acl
     */
    public function denyAclView()
    {
        return view('Backend.View::dashboard.denyAclView');
    }

    /**
     * return response json when user have not permission
     */
    public function denyAcl()
    {
        $rs = ['rs'=>System::FAIL, 'msg'=>[__('Backend.Lang::lang.msg.not_permission')]];
        return response()->json($rs);
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        Cache::flush();
        Config::where('slug', 'is_cache_need_deleted')->update(['value'=>System::CACHE_NOT_NEED_DELETE]);
        Session::flash('msg', __('Backend.Lang::lang.msg.delete_cache_success'));
        return redirect()->back();//redirect same page
    }
}