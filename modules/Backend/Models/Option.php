<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class Option extends AppModel
{
    protected $table = 'option';
    public $timestamps = false;

    const TYPE_SELECT = 1;
    const TYPE_RADIO = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_MULTI_SELECT = 4;

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
            ['column'=>'type', 'name'=>__('Backend.Lang::lang.field.type'),
                'partial'=>'Backend.View::share.optionType'],
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
            $data = Option::find($id);
        }
        $type = self::getOptionType();
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['number', 'sort_order', [], System::NO, [], 0],
            ['select', 'type', $type, System::NO, [], 1],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['template'] = 'Backend.View::group.option.form';
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['itemName'] = 'value';
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
                        'price' => $row->price,
                        'type' => $row->type,
                        'sort_order' => $i,
                        'option_id' => $id
                    ];
                    $i++;
                }
            } else {
                foreach ($item as $row) {
                    if ($row->id != 0) {//update
                        $dataUpdate = [
                            'name' => $row->name,
                            'price' => $row->price,
                            'type' => $row->type,
                            'sort_order' => $i,
                            'option_id' => $id
                        ];
                        OptionValue::where('id', $row->id)->update($dataUpdate);
                    } else {//insert
                        $dataInsert[] = [
                            'name' => $row->name,
                            'price' => $row->price,
                            'type' => $row->type,
                            'sort_order' => $i,
                            'option_id' => $id
                        ];
                    }
                    $i++;
                }
            }
            if (!empty($dataInsert)) {
                OptionValue::insert($dataInsert);
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
            $model = new Option();
            if ($data['id'] != 0) {
                $model = self::find($data['id']);
            }
            $model->name = $data['name'];
            $model->type = $data['type'];
            $model->sort_order = $data['sort_order'];
            $model->save();
            $id = $model->id;
            self::saveItem($data, $id);
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            $rs = ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
            return $rs;
        }
    }

    /**
     * Get option type
     */
    public static function getOptionType()
    {
        $data = [
            self::TYPE_SELECT => __('Backend.Lang::lang.option.select'),
            self::TYPE_RADIO => __('Backend.Lang::lang.option.radio'),
            self::TYPE_CHECKBOX => __('Backend.Lang::lang.option.checkbox'),
            self::TYPE_MULTI_SELECT => __('Backend.Lang::lang.option.multi_select')
        ];
        return $data;
    }

    /**
     * Display option type
     */
    public static function displayOptionType($type)
    {
        $optionTypeArray = self::getOptionType();
        $text = '';
        if (array_key_exists($type, $optionTypeArray)) {
            $text = $optionTypeArray[$type];
        }
        return $text;
    }

    /**
     * Get option select
     */
    public static function getOptionSelect()
    {
        $data = self::all();
        $first = [0=>trans('Backend.Lang::lang.product.choose_option')];
        $option = Functions::convertArrayKeyValue($data, 'id', 'name');
        $optionSelect = array_merge($first, $option);
        $optionType = Functions::convertArrayKeyValue($data, 'id', 'type');
        return [
            'select' => $optionSelect,
            'type' => $optionType
        ];
    }


}