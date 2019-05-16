<?php

namespace Modules\Frontend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Facades\OrderFacades;
use Modules\Backend\Models\Order;
use Modules\Backend\Models\User;
use Modules\Backend\Models\UserExtend;
use Modules\Frontend\Classes\Frontend;
use Shipu\Themevel\Facades\Theme as STheme;

class UserFacades extends Model
{
    /**
     * Save user
     */
    public static function saveUser($data)
    {
        DB::beginTransaction();
        $code = Functions::generateRandomString(10);
        try {
            $user = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'active_code' => $code,
                'active_code_expire' => Carbon::now()->addDay(),
                'status' => System::DISABLE
            ];
            $id = User::insertGetId($user);
            unset($data['_token']);
            unset($data['password']);
            unset($data['password_confirmation']);
            $data['user_id'] = $id;
            $userExtendModel = new UserExtend();
            $userExtendModel::insert($data);
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'code'=>$code];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Send mail register success
     */
    public static function sendRegisterMail($email, $name, $code)
    {
        $url = url('/active/'.$code);
        $params = [
            'email' => $email,
            'name' => $name,
            'subject' => STheme::lang('lang.subject.register_success'),
            'data' => ['url'=>$url],
            'template' => 'mails.registerSuccess'
        ];
        System::sendMail($params);
    }

    /**
     * Validate and save user
     */
    public static function doRegister($data)
    {
        $msgValidate = [];
        $rule = [
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|confirmed|min:6',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric',
            'address' => 'required',
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            $rs = self::saveUser($data);
            if ($rs['rs'] == System::SUCCESS) {
                $code = $rs['code'];
                $name = $data['first_name'].' '.$data['last_name'];
                self::sendRegisterMail($data['email'], $name, $code);
                Session::flash(System::FLASH_SUCCESS, STheme::lang('lang.msg.register_success'));
                $rs = [
                    'rs'=>System::SUCCESS,
                    'msg'=>'',
                    'redirect_url'=>'/register'
                ];
            }
            return $rs;
        }
    }

    /**
     * Active user
     */
    public static function activeUser($code)
    {
        $user = User::where('active_code', $code)
            ->where('active_code_expire', '>', Carbon::now())
            ->first();
        if (!empty($user)) {
            $user->status = System::ENABLE;
            $user->active_code = null;
            $user->save();
            return ['flash'=>System::FLASH_SUCCESS, 'msg'=>STheme::lang('lang.msg.active_success')];
        } else {
            return ['flash'=>System::FLASH_ERROR, 'msg'=>STheme::lang('lang.msg.user_not_exits')];
        }
    }

    /**
     * Validate and do login
     */
    public static function doLogin($data)
    {
        $msgValidate = [];
        $rule = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            $rs = \Modules\Frontend\Auth\User::doLogin($data);
            return $rs;
        }
    }

    /**
     * Send email to reset password
     */
    public static function sendEmailToResetPassword($data)
    {
        $email = $data['email'];
        $user = \Modules\Frontend\Auth\User::where('email', $email)
            ->first();
        if (empty($user)) {
            return ['rs'=>System::FAIL, 'msg'=>STheme::lang('lang.msg.user_not_exists')];
        } else {
            DB::beginTransaction();
            try {
                $newPass = Functions::generateRandomString(6);
                $user->password = Hash::make($newPass);
                $user->save();
                $params = [
                    'email' => $email,
                    'name' => $user->first_name.' '.$user->last_name,
                    'subject' => STheme::lang('lang.general.reset_password'),
                    'data' => ['newPass'=>$newPass],
                    'template' => 'mails.resetPassword'
                ];
                DB::commit();
                System::sendMail($params);
                if (Mail::failures()) {// check for failures
                    return ['rs'=>System::FAIL, 'msg'=>STheme::lang('lang.msg.mail_not_send')];
                }
                Session::flash(System::FLASH_SUCCESS, STheme::lang('lang.msg.send_mail_forgot_password_success'));
                return [
                    'rs'=>System::SUCCESS,
                    'msg'=>'',
                    'redirect_url'=>'/forgot-password'
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
            }
        }
    }

    /**
     * Validate then send email to reset password
     */
    public static function resetPassword($data)
    {
        $msgValidate = [];
        $rule = [
            'email' => 'required|email'
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            $rs = self::sendEmailToResetPassword($data);
            return $rs;
        }
    }

    /**
     * Validate and change password
     */
    public static function changePassword($data)
    {
        $msgValidate = [];
        $rule = [
            'password' => 'required|confirmed|min:6'
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            $user = Auth::guard('users')->user();
            $user->update(['password' => Hash::make($data['password'])]);
            Session::flash(System::FLASH_SUCCESS, STheme::lang('lang.msg.change_password_success'));
            return [
                'rs'=>System::SUCCESS,
                'msg'=>'',
                'redirect_url'=>'/user/change-password'
            ];
        }
    }

    /**
     * do save address
     */
    public static function doSaveAddress($data)
    {
        try {
            $model = new UserExtend();
            if ($data['id'] != 0) {
                $model = UserExtend::find($data['id']);
            }
            $model->user_id = $data['user_id'];
            $model->first_name = $data['first_name'];
            $model->last_name = $data['last_name'];
            $model->phone = $data['phone'];
            $model->email = $data['email'];
            $model->address = $data['address'];
            $model->save();
            if ($data['current_action'] == '/checkout') {
                $actionRedirect = '/checkout';
            } else {
                $actionRedirect = '/user/address-manager';
            }
            return [
                'rs'=>System::SUCCESS,
                'msg'=>STheme::lang('lang.msg.save_address_success'),
                'redirect_url' => $actionRedirect
            ];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

    /**
     * Validate and save address
     */
    public static function saveAddress($data)
    {
        $msgValidate = [];
        $rule = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'address' => 'required'
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            return self::doSaveAddress($data);
        }
    }

    /**
     * Validate form address in checkout when user not login
     */
    public static function validateAddressNotLogin($data)
    {
        $msgValidate = [];
        $rule = [
            'billing_first_name' => 'required',
            'billing_last_name' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|numeric',
            'billing_address' => 'required'
        ];
        if (!isset($data['use_same_address_not_login'])) {
            $rule['shipping_first_name'] = 'required';
            $rule['shipping_last_name'] = 'required';
            $rule['shipping_email'] = 'required|email';
            $rule['shipping_phone'] = 'required|numeric';
            $rule['shipping_address'] = 'required';
        }
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        return $validateRs;
    }

    /**
     * List address
     */
    public static function listAddress()
    {
        $user = Auth::guard('users')->user();
        if (!empty($user)) {
            $userId = $user->id;
            $data = UserExtend::where('user_id', $userId)->get();
            return $data;
        }
        return [];
    }

    /**
     * List order
     */
    public static function listOrder()
    {
        $user = Auth::guard('users')->user();
        if (!empty($user)) {
            $userId = $user->id;
            $data = Order::with(['product:*', 'orderStatus:id,name'])
                ->where('user_id', $userId)
                ->paginate(System::PAGE_SIZE_DEFAULT);
            return $data;
        }
        return [];
    }

    /**
     * Check order id belong user id
     */
    public static function checkOrderIdBelongUserId($userIdCheck, $orderId)
    {
        $data = OrderFacades::getOrderDetail($orderId);
        if (!empty($data)) {
            $userId = $data['user_id'];
            if ($userId == $userIdCheck) {
                return $data;
            }
        }
        return [];
    }
}
