<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class Weight extends AppModel
{
    protected $table = 'weight';
    public $timestamps = false;

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
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name')],
            ['column'=>'unit', 'name'=>__('Backend.Lang::lang.field.unit')],
            ['column'=>'value', 'name'=>__('Backend.Lang::lang.field.value')]
        ];
        $rs = [
            'data' => $data,
            'field' => $field,
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
            ['text', 'name', [], System::YES],
            ['text', 'unit', [], System::YES],
            ['text', 'value', [], System::YES]
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
        $msgValidate = [
            'name.required' => __('Backend.Lang::lang.validate.field_required'),
            'unit.required' => __('Backend.Lang::lang.validate.field_required'),
            'value.required' => __('Backend.Lang::lang.validate.field_required'),
            'value.regex' => __('Backend.Lang::lang.validate.decimal'),
        ];
        $rule = [
            'name' => 'required',
            'unit' => 'required',
            'value' => 'required|regex:/^\d*(\.\d{1,2})?$/',
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
            $model = new Weight();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->unit = $data['unit'];
            $model->value = $data['value'];
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * weight select
     */
    public static function weightSelect()
    {
        $data = self::select('id', 'name')->get();
        $rs = Functions::convertArrayKeyValue($data, 'id', 'name');
        return $rs;
    }




}