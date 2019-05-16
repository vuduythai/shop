<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class Shipping extends AppModel
{
    protected $table = 'ship_rule';
    public $timestamps = false;

    const TYPE_PRICE = 1;
    const TYPE_GEO = 2;
    const TYPE_WEIGHT_BASED = 3;
    const TYPE_PER_ITEM = 4;
    const TYPE_GEO_WEIGHT_BASED = 5;

    const WEIGHT_TYPE_FIXED = 1;
    const WEIGHT_TYPE_RATE = 2;


    public function geo()
    {
        return $this->belongsTo('Modules\Backend\Models\Geo', 'geo_zone_id');
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
            ['column'=>'above_price', 'name'=>__('Backend.Lang::lang.field.above_price')],
            ['column'=>'', 'name'=>__('Backend.Lang::lang.field.geo_zone_id'),
                'relation'=>'geo,name'],
            ['column'=>'cost', 'name'=>__('Backend.Lang::lang.field.cost')],
            ['column'=>'type', 'name'=>__('Backend.Lang::lang.field.type')],
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
            $data = Shipping::find($id);
        }
        $geo = Geo::geoSelect();
        $shippingType = Shipping::typeSelect();
        $weightBasedType = Shipping::weightBasedTypeSelect();
        $commentWeightBased = __('Backend.Lang::lang.comment.weight_based');
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['text', 'above_price', [], System::YES],
            ['select', 'type', $shippingType],
            ['select', 'geo_zone_id', $geo],
            ['select', 'weight_type', $weightBasedType],
            ['text', 'weight_based', [], System::YES, [], '', $commentWeightBased],
            ['text', 'cost', [], System::YES],
            ['switch', 'status', [], System::NO, [], System::ENABLE],
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
            'name' => 'required',
            'cost' => 'required'
        ];
        if ($data['type'] == Shipping::TYPE_PRICE) {
            $msgValidate['above_price.required'] = __('Backend.Lang::lang.validate.field_required');
            $rule['above_price'] = 'required';
        }
        if ($data['type'] == Shipping::TYPE_WEIGHT_BASED ||
            $data['type'] == Shipping::TYPE_GEO_WEIGHT_BASED) {
            array_pop($msgValidate);
            array_pop($rule);
            $msgValidate['weight_based.required'] = __('Backend.Lang::lang.validate.field_required');
            $rule['weight_based'] = 'required';
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
            $model = new Shipping();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->above_price = !empty($data['above_price']) ? $data['above_price'] : 0;
            $model->geo_zone_id = !empty($data['geo_zone_id']) ? $data['geo_zone_id'] : 0;
            $model->weight_type = !empty($data['weight_type']) ? $data['weight_type'] : 0;
            $model->weight_based = !empty($data['weight_based']) ? $data['weight_based'] : 0;
            $model->cost = $data['cost'];
            $model->type = $data['type'];
            $model->status = !empty($data['status']) ? $data['status'] : System::STATUS_UNACTIVE;
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Return Type select
     */
    public static function typeSelect()
    {
        $data = [
            self::TYPE_PRICE => __('Backend.Lang::lang.shipping.type_price'),
            self::TYPE_GEO => __('Backend.Lang::lang.shipping.type_geo'),
            self::TYPE_WEIGHT_BASED => __('Backend.Lang::lang.shipping.type_weight_based'),
            self::TYPE_PER_ITEM => __('Backend.Lang::lang.shipping.type_per_item'),
            self::TYPE_GEO_WEIGHT_BASED => __('Backend.Lang::lang.shipping.type_geo_weight_based'),
        ];
        return $data;
    }

    /**
     * Return weight based type
     */
    public static function weightBasedTypeSelect()
    {
        $data = [
            self::WEIGHT_TYPE_FIXED => __('Backend.Lang::lang.shipping.weight_type_fixed'),
            self::WEIGHT_TYPE_RATE => __('Backend.Lang::lang.shipping.weight_type_rate')
        ];
        return $data;
    }

    /**
     * Get ship rule by id
     */
    public static function getShipRuleNameById($id)
    {
        $data = self::select('name')
            ->where('id', $id)
            ->first();
        $rs = [];
        if (!empty($data)) {
            $rs = $data->toArray();
        }
        return $rs['name'];
    }
}