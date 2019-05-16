<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;
use Illuminate\Support\Facades\Mail;

class User extends AppModel
{
    protected $table = 'users';

    /**
     * key 'relation' is a string: 'relation,field_name'
     */
    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'first_name', 'name'=>__('Backend.Lang::lang.field.first_name')],
            ['column'=>'last_name', 'name'=>__('Backend.Lang::lang.field.last_name')],
            ['column'=>'email', 'name'=>__('Backend.Lang::lang.field.description')],
            ['column'=>'status', 'name'=>__('Backend.Lang::lang.field.status'),
                'partial'=>'Backend.View::share.status']
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
            $data = User::find($id);
        }
        $arrayField = [
            ['text', 'first_name', [], System::YES],
            ['text', 'last_name', [], System::YES],
            ['text', 'email', [], System::YES],
            ['switch', 'status', [], System::NO, [], System::ENABLE],
            ['image', 'avatar', []],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [];
        $rule = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users'
        ];
        if ($data['id'] != 0) {
            array_pop($rule);
            $rule['email'] = 'required|email|unique:users,email,'.$data['id'];//unique update
        }
        return AppModel::returnValidateResult($data, $rule, $msgValidate, $controller, $close);
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        try {
            $id = $data['id'];
            $model = new User();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->first_name = $data['first_name'];
            $model->last_name = $data['last_name'];
            $model->email = $data['email'];
            $model->status = isset($data['status']) ? $data['status'] : System::NO;
            $model->avatar = $data['avatar'];
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }


}