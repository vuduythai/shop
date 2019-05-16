<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\System;

class TaxRate extends AppModel
{
    protected $table = 'tax_rate';
    public $timestamps = false;

    public function geo()
    {
        return $this->belongsTo('Modules\Backend\Models\Geo', 'geo_zone_id');
    }

    /**
     * Get item by id
     */
    public static function getItemById($id)
    {
        return self::find($id);
    }

    /**
     * Get item by parent id
     */
    public static function getItemByParentId($parentId)
    {
        $itemTable = with(new TaxRate())->getTable();
        $relationTable = with(new TaxRule())->getTable();
        $data = DB::table($itemTable.' AS i')
            ->leftJoin($relationTable.' AS r', 'r.tax_rate_id', '=', 'i.id')
            ->where('r.tax_class_id', $parentId)
            ->get();
        return $data;
    }

    /**
     * Get all item
     */
    public static function getItemPage($page)
    {
        return self::paginate(System::PAGE_SIZE_DEFAULT, ['*'], 'page', $page);
    }

    /**
     * Delete item
     */
    public static function deleteItem($idArray)
    {
        DB::beginTransaction();
        try {
            TaxRule::whereIn('tax_rate_id', $idArray)->delete();
            self::whereIn('id', $idArray)->delete();
            DB::commit();
            return ['rs'=>System::SUCCESS];
        } catch (\Exception $e) {
            DB::rollback();
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Save item
     */
    public static function saveItem($data, $action, $id)
    {
        $model = new TaxRate();
        if ($action == System::ACTION_UPDATE) {
            $model = self::find($id);
        }
        $model->name = $data['name'];
        $model->type = $data['type'];
        $model->geo_zone_id = $data['geo_zone_id'];
        $model->rate = $data['rate'];
        $model->save();
        return ['rs'=>System::SUCCESS, 'id'=>$model->id];
    }

    /**
     * Validate Item in many-to-many form when create parent
     */
    public static function validateItemAndSave($data, $action, $id)
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
            return self::saveItem($data, $action, $id);
        }
    }

    /**
     * Get geo
     */
    public static function getGeo()
    {
        return Geo::all();
    }


}