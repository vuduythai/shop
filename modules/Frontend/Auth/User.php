<?php
namespace Modules\Frontend\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\System;
use Shipu\Themevel\Facades\Theme as STheme;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $fillable = ['username', 'password'];

    /**
     * Do login
     */
    public static function doLogin($data)
    {
        $user = self::where('email', $data['email'])
            ->first();
        if (empty($user)) {
            return ['rs'=>System::FAIL, 'msg' => [STheme::lang('lang.msg.user_not_exists')]];
        } else {
            if ($user->status == System::DISABLE) {
                return ['rs'=>System::FAIL, 'msg' => [STheme::lang('lang.msg.user_not_active')]];
            }
        }
        //laravel >= 5.5 need email and password for function attempt()
        $dataLogin = [
            'email'=>$data['email'],
            'password' => $data['password']
        ];
        if (Auth::guard('users')->attempt($dataLogin)) {
            $user = Auth::guard('users')->user();
            Session::put('user', $user);
            Session::flash('notify_flash_msg', STheme::lang('lang.msg.login_success'));
            return ['rs' => System::SUCCESS, 'msg'=>'', 'redirect_url' => $data['redirect_url']];//login success
        } else {
            return ['rs' => System::FAIL, 'msg' => [STheme::lang('lang.msg.pass_wrong')]];
        }
    }

}