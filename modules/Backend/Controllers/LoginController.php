<?php

namespace Modules\Backend\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Models\BackendUser;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Illuminate\Support\Facades\View;

class LoginController extends Controller
{

    public function __construct()
    {
        $data['msg_js'] = System::getMsgJs();
        $data['config'] = Config::getConfigByKey('config', '');
        return View::share('share', $data);
    }

    /**
     * Load view login
     */
    public function login()
    {
        if (!Auth::guard('admin')->check()) {
            return view('Backend.View::login.index');
        } else {
            return redirect(config('app.admin_url').'/dashboard');
        }
    }

    /**
     * Do login
     */
    public function doLogin(Request $request)
    {
        $formData = $request['formData'];
        $data = [];
        foreach ($formData as $row) {
            $data[$row['name']] = $row['value'];
        }
        $validate = BackendUser::validateFormLogin($data);
        if ($validate['rs'] == System::SUCCESS) {
            $rs = BackendUser::doLogin($data);
            if ($rs['rs'] == System::SUCCESS) {
                Session::flash('msg', trans('Backend.Lang::lang.msg_js.login_successful'));
            } else {
                return response()->json($rs);
            }
        } else {
            return response()->json($validate);
        }
        return response()->json($rs);
    }

    /**
     * Do logout
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        //Session::flush();//don't use this when has multi authen
        Session::forget('admin');
        return redirect()->intended(config('app.admin_url').'/login');
    }
}