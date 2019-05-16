<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AclResource;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class Role extends AppModel
{
    protected $table = 'roles';
    public $timestamps = false;

    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name')]
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
            $data = self::find($id);
        }
        $arrayField = [
            ['text', 'name', [], System::YES]
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form = System::addPermissionForForm($form, $data, $id);
        $form['template'] = 'Backend.View::group.role.form';
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [
            'name.required' => __('Backend.Lang::lang.validate.field_required')
        ];
        $rule = [
            'name' => 'required'
        ];
        return AppModel::returnValidateResult($data, $rule, $msgValidate, $controller, $close);
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        try {
            $id = $data['id'];
            $model = new Role();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $permission = System::convertPermission($data);
            $model->permission = json_encode($permission);
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Get role for filter
     */
    public static function getRoles()
    {
        $data = self::select('id', 'name')
            ->get()->toArray();
        $rs = [];
        foreach ($data as $row) {
            $rs[$row['id']] = $row['name'];
        }
        return $rs;
    }

    /**
     * Generate drop down box for filter
     */
    public static function roleSelect()
    {
        $roles = self::getRoles();
        $rs = [];
        $rs[0] = __('Backend.Lang::lang.select.select_roles');
        foreach ($roles as $key => $value) {
            $rs[$key] = $value;
        }
        return $rs;
    }

    /**
     * Get permission by role id
     */
    public static function getPermissionByRoleId($roleId)
    {
        $data = self::select('permission')
            ->where('id', $roleId)
            ->first();
        $permission = [];
        if (!empty($data)) {
            $permission = json_decode($data->permission, true);
        }
        return $permission;
    }
}