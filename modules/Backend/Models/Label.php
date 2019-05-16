<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class Label extends AppModel
{
    protected $table = 'label';
    public $timestamps = false;

    const TYPE_IMAGE = 1;
    const TYPE_TEXT_ON_IMAGE = 2;

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
                'partial'=>'Backend.View::share.image'],
            ['column'=>'type', 'name'=>__('Backend.Lang::lang.field.type'),
                'partial'=>'Backend.View::share.productLabelType'],
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
        $type = self::typeSelect();
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['text', 'text_display', [], System::NO],
            ['image', 'image', []],
            ['textarea', 'css_inline_text', []],
            ['textarea', 'css_inline_image', []],
            ['select', 'type', $type],
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
            $model = new Label();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->text_display = $data['text_display'];
            $model->image = $data['image'];
            $model->css_inline_text = $data['css_inline_text'];
            $model->css_inline_image = $data['css_inline_image'];
            $model->type = $data['type'];
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }


    /**
     * Return type select
     */
    public static function typeSelect()
    {
        return [
            self::TYPE_IMAGE => __('Backend.Lang::lang.product_label.image'),
            self::TYPE_TEXT_ON_IMAGE => __('Backend.Lang::lang.product_label.text_on_image')
        ];
    }

    /**
     * return text for label id
     */
    public static function labelTypeText($id)
    {
        $type = self::typeSelect();
        if (array_key_exists($id, $type)) {
            return $type[$id];
        }
        return '';
    }

    /**
     * label select
     */
    public static function labelSelect()
    {
        $data = self::all();
        return Functions::convertArrayKeyValue($data, 'id', 'name');
    }

    /**
     * Get product label
     */
    public static function getAllProductLabel()
    {
        $data = self::get()->toArray();
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[$row['id']] = $row;
            }
        }
        return $rs;
    }

}