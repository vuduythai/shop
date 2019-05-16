<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class Attribute extends AppModel
{
    protected $table = 'attribute';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    const IS_NOT_FOR_DISPLAY = 0;
    const IS_FOR_DISPLAY = 1;

    const IS_NOT_FILTER = 0;
    const IS_FILTER = 1;

    const TYPE_TEXT = 1;
    const TYPE_COLOR = 2;

    public function attributeGroup()
    {
        return $this->belongsTo('Modules\Backend\Models\AttributeGroup', 'attribute_group_id');
    }

    /**
     * Has many property
     */
    public function property()
    {
        return $this->hasMany('Modules\Backend\Models\AttributeProperty', 'attribute_id', 'id');
    }

    /**
     * Get type display
     */
    public static function getType()
    {
        return [
            self::TYPE_TEXT => trans('Backend.Lang::lang.attribute.text'),
            self::TYPE_COLOR => trans('Backend.Lang::lang.attribute.color'),
        ];
    }

    /**
     * Display type text
     */
    public static function displayTypeText($type)
    {
        $array = [
            self::TYPE_TEXT => trans('Backend.Lang::lang.attribute.text'),
            self::TYPE_COLOR => trans('Backend.Lang::lang.attribute.color'),
        ];
        if (array_key_exists($type, $array)) {
            return $array[$type];
        }
        return '';
    }

    /**
     * Display option value
     */
    public static function displayPropertyValue($value, $valueType)
    {
        if ($valueType == self::TYPE_COLOR) {
            $display = '<div style="background-color: '.$value.
                ';border:1px #cccccc solid;width:30px;height:30px"></div>';
        } else {
            $display = $value;
        }
        return $display;
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
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name')],
            ['column'=>'is_filter', 'name'=>__('Backend.Lang::lang.attribute.is_filter_th'),
                'partial'=>'Backend.View::share.isFilter'],
            ['column'=>'is_display', 'name'=>__('Backend.Lang::lang.attribute.is_display'),
                'partial'=>'Backend.View::share.isDisplay'],
            ['column'=>'attribute_group_id', 'name'=>__('Backend.Lang::lang.attribute.attribute_group_id'),
                'relation'=>'attributeGroup,name']
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
        $type = self::getType();
        $yesOrNo = System::yesOrNoArray();
        $commentIsFilter = __('Backend.Lang::lang.comment.is_filter');
        $attributeGroup = AttributeGroup::getAttributeGroupSelect();
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['select', 'type', $type, System::NO, [], System::YES],
            ['radio', 'is_filter', $yesOrNo, System::NO, [], System::NO, $commentIsFilter],
            ['switch', 'is_display', [], System::NO, [], System::YES],
            ['select', 'attribute_group_id', $attributeGroup, System::NO, [], '']
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['template'] = 'Backend.View::group.attribute.form';
        $form['itemName'] = 'property';
        return $form;
    }


    /**
     * save item
     */
    public static function saveItem($data, $id)
    {
        if (isset($data['item_json_data'])) {
            $itemJson = $data['item_json_data'];
            $item = json_decode($itemJson);
            $dataInsert = [];
            $i = 0;
            if ($data['id'] == 0) {
                foreach ($item as $row) {
                    $dataInsert[] = [
                        'name' => $row->name,
                        'value' => $row->value,
                        'type' => $row->type,
                        'sort_order' => $i,
                        'attribute_id' => $id
                    ];
                    $i++;
                }
            } else {
                foreach ($item as $row) {
                    if ($row->id != 0) {//update
                        $dataUpdate = [
                            'name' => $row->name,
                            'value' => $row->value,
                            'type' => $row->type,
                            'sort_order' => $i,
                            'attribute_id' => $id
                        ];
                        AttributeProperty::where('id', $row->id)->update($dataUpdate);
                    } else {//insert
                        $dataInsert[] = [
                            'name' => $row->name,
                            'value' => $row->value,
                            'type' => $row->type,
                            'sort_order' => $i,
                            'attribute_id' => $id
                        ];
                    }
                    $i++;
                }
            }
            if (!empty($dataInsert)) {
                AttributeProperty::insert($dataInsert);
            }
        }
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        DB::beginTransaction();
        try {
            $model = new Attribute();
            if ($data['id'] != 0) {
                $model = self::find($data['id']);
            }
            $model->name = $data['name'];
            $model->type = $data['type'];
            $model->is_filter = $data['is_filter'];
            if ($data['is_filter'] == Attribute::IS_NOT_FILTER) {
                $model->is_display = System::YES;
            } else {
                $model->is_display = isset($data['is_display']) ? $data['is_display'] : System::NO;
            }
            $model->attribute_group_id = $data['attribute_group_id'];
            $model->save();
            $id = $model->id;
            self::saveItem($data, $id);
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            $rs = ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
            return $rs;
        }
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
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            return self::saveRecord($data, $close);
        }
    }

}