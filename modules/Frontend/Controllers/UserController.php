<?php

namespace Modules\Frontend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Modules\Backend\Core\Twig;
use Modules\Backend\Models\Config;
use Shipu\Themevel\Facades\Theme as STheme;
use Modules\Frontend\Facades\UserFacades;
use Modules\Frontend\FrontendController;

class UserController extends FrontendController
{

    /**
     * Register
     */
    public function register()
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.register');
        return view('pages.register', $data);
    }

    /**
     * Active user
     */
    public function active(Request $request)
    {
        $code = $request->code;
        $rs = UserFacades::activeUser($code);
        return redirect('/login')->with($rs['flash'], $rs['msg']);
    }

    /**
     * Login
     */
    public function login()
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.login');
        return view('pages.login', $data);
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::guard('users')->logout();
        Session::forget('user');
        //display notify flash message
        Session::flash('notify_flash_msg', STheme::lang('lang.msg.logout_success'));
        return redirect('/');
    }

    /**
     * Forgot password
     */
    public function forgotPassword()
    {
        $data['breadcrumbName'] = STheme::lang('lang.user.forgot_password');
        return view('pages.forgotPassword', $data);
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $data['breadcrumbName'] = STheme::lang('lang.user.change_password');
        return view('pages.changePassword', $data);
    }

    /**
     * Address manager
     */
    public function addressManager()
    {
        $data['address'] = UserFacades::listAddress();
        $data['breadcrumbName'] = STheme::lang('lang.user.address_manager');
        return view('pages.addressManager', $data);
    }

    /**
     * Order Manager
     */
    public function orderManager()
    {
        $data['breadcrumbName'] = STheme::lang('lang.user.order_manager');
        $data['order'] = UserFacades::listOrder();
        return view('pages.orderManager', $data);
    }

    /**
     * Order detail
     */
    public function orderDetail(Request $request)
    {
        $user = Auth::guard('users')->user();
        if (!empty($user)) {
            $userId = $user->id;
            $orderId = $request->id;
            $orderCheck = UserFacades::checkOrderIdBelongUserId($userId, $orderId);
            if (!empty($orderCheck)) {
                $data['order'] = $orderCheck;
                $data['breadcrumbName'] = STheme::lang('lang.user.order_detail');
                $data['urlPrintInvoice'] = URL::to('/order/invoice/'.$orderId);
                return view('pages.orderDetail', $data);
            } else {
                return redirect('/404');
            }
        }
    }

    /**
     * Order invoice
     */
    public function orderInvoice(Request $request)
    {
        $user = Auth::guard('users')->user();
        if (!empty($user)) {
            $userId = $user->id;
            $orderId = $request->id;
            $orderCheck = UserFacades::checkOrderIdBelongUserId($userId, $orderId);
            if (!empty($orderCheck)) {
                $data['order'] = $orderCheck;
                $data['created_at'] = date('Y-m-d', strtotime($orderCheck->created_at));
                $css = Config::getConfigByKeyCache('invoice_css', '');
                $template = Config::getConfigByKeyCache('invoice_template', '');
                return Twig::parse($css.''.$template, $data);//twig parse code from string
            } else {
                return redirect('/404');
            }
        }
    }

}