<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class Brand extends AppModel
{
    protected $table = 'brand';
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
            ['column'=>'image', 'name'=>__('Backend.Lang::lang.field.image'),
                'partial'=>'Backend.View::share.image', 'sort'=>'no-sort'],
            ['column'=>'sort_order', 'name'=>__('Backend.Lang::lang.field.sort_order')],
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
            ['text', 'name', [], System::YES],
            ['number', 'sort_order', [], System::NO, [], 0],
            ['image', 'image', []],
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
            $model = new Brand();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->image = $data['image'];
            $model->sort_order = $data['sort_order'];
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Brand select
     */
    public static function brandSelect()
    {
        $data = self::all();
        $brand[0] = trans('Backend.Lang::lang.select.select_brand');
        $brandData = Functions::convertArrayKeyValue($data, 'id', 'name');
        $rs = array_merge($brand, $brandData);
        return $rs;
    }

    /**
     * Get image by id
     */
    public static function getImageById($id)
    {
        $data = self::select('image')->where('id', $id)->first();
        if (!empty($data)) {
            return $data->image;
        }
        return '';
    }
}