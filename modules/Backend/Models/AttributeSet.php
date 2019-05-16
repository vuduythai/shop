<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class AttributeSet extends AppModel
{
    protected $table = 'attribute_set';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    /**
     * Get all attribute group
     */
    public static function getAllAttributeSet()
    {
        return self::select('id', 'name')->get()->toArray();
    }

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
            $data = AttributeSet::find($id);
        }
        $arrayField = [
            ['text', 'name', [], System::YES]
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);

        $attributeChosen = [];
        $attributeChosenArray = [];
        if ($id != '') {
            if (!empty($data->attribute_json)) {
                $attributeChosenArray = json_decode($data->attribute_json, true);
                $attributeChosen = self::getAttributeSetToEdit($attributeChosenArray);
            }
        }
        $form['attributeChosen'] = $attributeChosen;
        $form['attribute'] = self::getAttributeExceptEdited($attributeChosenArray);
        $form['template'] = 'Backend.View::group.attribute_set.form';
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
        DB::beginTransaction();
        try {
            $id = $data['id'];
            $model = new AttributeSet();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            //save attribute set
            $attribute = [];
            if (array_key_exists('drag_attr_id', $data)) {
                foreach ($data['drag_attr_id'] as $row) {
                    $attribute[] = $row;
                }
            }
            $model->attribute_json = json_encode($attribute);
            $model->save();
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Get attribute group for form select
     */
    public static function getAttributeSetSelect()
    {
        $data = self::all();
        $rs = Functions::convertArrayKeyValue($data, 'id', 'name');
        return $rs;
    }

    /**
     * Get attribute set to edit
     */
    public static function getAttributeSetToEdit($attributeArray)
    {
        $data = Attribute::select('id', 'name')
            ->whereIn('id', $attributeArray)
            ->get();
        //re sort attribute by attribute Array
        $rs = [];
        foreach ($attributeArray as $row) {
            foreach ($data as $attr) {
                if ($row == $attr->id) {
                    $rs[] = $attr;
                }
            }
        }
        return $rs;
    }

    /**
     * Get attribute except attribute that edited
     */
    public static function getAttributeExceptEdited($attributeArray)
    {
        $query = Attribute::select('id', 'name');
        if (!empty($attributeArray)) {
            $query->whereNotIn('id', $attributeArray);
        }
        $data = $query->get();
        return $data;
    }

}