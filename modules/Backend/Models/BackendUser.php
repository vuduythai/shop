<?php
namespace Modules\Backend\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Backend\Core\AclResource;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;
use Modules\Backend\Core\AppModel;
use Illuminate\Support\Facades\Validator;

class BackendUser extends Authenticatable
{
    protected $table = 'backend_users';
    protected $fillable = ['username', 'password'];

    /**
     * Validate form login
     */
    public static function validateFormLogin($data)
    {
        $msgValidate = [];
        $rule = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            return ['rs'=>System::SUCCESS];
        }
    }

    /**
     * Do login
     */
    public static function doLogin($request)
    {
        $user = self::where('email', $request['email'])
            ->where('status', System::STATUS_ACTIVE)
            ->first();
        if (empty($user)) {
            return ['rs'=>System::FAIL, 'msg' => [__('Backend.Lang::lang.validate.user_not_exists')]];
        }
        //laravel >= 5.5 need email and password to attempt()
        $data = [
            'email'=>$request['email'],
            'password' => $request['password'],
            'status' => System::STATUS_ACTIVE
        ];
        if (Auth::guard('admin')->attempt($data)) {
            $user = $user->toArray();
            Session::put('admin', $user);
            return ['rs' => System::SUCCESS];//login success
        } else {
            return ['rs' => System::FAIL, 'msg' => [__('Backend.Lang::lang.validate.password_wrong')]];
        }
    }

    public function role()
    {
        return $this->belongsTo('Modules\Backend\Models\Role');
    }

    /**
     * key 'relation' is a string: 'relation,field_name'
     */
    public static function getList($params)
    {
        $query = self::with('role:id,name');
        $query = $query->orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name')],
            ['column'=>'email', 'name'=>__('Backend.Lang::lang.field.email')],
            ['column'=>'', 'name'=>__('Backend.Lang::lang.field.role'), 'relation'=>'role,name'],
            ['column'=>'status', 'name'=>__('Backend.Lang::lang.field.status'),
                'partial'=>'Backend.View::share.status'],
        ];
        $rs = [
            'data' => $data,
            'field' => $field
        ];
        return $rs;
    }

    /**
     * Return form to create and edit
     */
    public static function formCreate($request, $controller, $id = '')
    {
        $data = new \stdClass();
        if ($id != '') {//edit
            $data = BackendUser::find($id);
            $data->password = '';//not assign password when update
        }
        $roles = Role::roleSelect();
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['text', 'email', [], System::YES],
            ['password', 'password', [], System::YES],
            ['password', 'password_confirmation', [], System::YES],
            ['switch', 'status', [], System::NO, [], System::ENABLE],
            ['select', 'role_id', $roles]
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form = System::addPermissionForForm($form, $data, $id);
        $form['template'] = 'Backend.View::group.backend_user.form';
        $form['emailEdit'] = isset($data->email) ? $data->email : '';
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [
        ];
        $rule = [
            'name' => 'required',
            'email' => 'required|email|unique:backend_users',
            'role_id' => 'required',
            'password' => 'required|confirmed',//just name field password retype is 'password_confirmation'
        ];
        if ($data['id'] != 0) {//update
            $rule['email'] = 'required|email|unique:backend_users,email,'.$data['id'];//unique update
            $rule['password'] = 'confirmed';
        }
        return AppModel::returnValidateResult($data, $rule, $msgValidate, $controller, $close);
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        if ($data['role_id'] == 0) {
            return ['rs'=>System::FAIL, 'msg'=>[__('Backend.Lang::lang.msg.have_to_choose_role')]];
        }
        try {
            $id = $data['id'];
            $model = new BackendUser();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->email = $data['email'];
            if (!empty($data['password'])) {
                $model->password = Hash::make($data['password']);
            }
            $model->status = isset($data['status']) ? $data['status'] : System::NO;
            $model->role_id = $data['role_id'];
            $permission = System::convertPermission($data);
            $model->permission = json_encode($permission);
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Change password of backend user
     */
    public static function changePassword($id, $strRandom)
    {
        $user = self::find($id);
        $user->password = Hash::make($strRandom);
        $user->save();
        return $user;
    }

    /**
     * Send email reset password
     */
    public static function sendEmailResetPassword($email, $id, $strRandom)
    {
        $user = self::changePassword($id, $strRandom);
        $params = [
            'email' => $email,
            'name' => $user->name,
            'subject' => __('Backend.Lang::lang.backend_user.reset_password'),
            'data' => ['newPass'=>$strRandom],
            'template' => 'Backend.View::mail.resetPassword'
        ];
        System::sendMail($params);
        if (Mail::failures()) {// check for failures
            return ['rs'=>System::FAIL, 'msg'=>[__('Backend.Lang::lang.backend_user.mail_not_send')]];
        }
        return ['rs'=>System::SUCCESS, 'msg'=>[__('Backend.Lang::lang.backend_user.send_email_success')]];
    }

}