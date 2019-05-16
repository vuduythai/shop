<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\System;

class OptionValue extends AppModel
{
    protected $table = 'option_value';
    public $timestamps = false;

    public function option()
    {
        return $this->belongsTo('Modules\Backend\Models\Option', 'option_id');
    }

    /**
     * Get item by parent id
     */
    public static function getItemByParentId($parentId)
    {
        $data = self::where('option_id', $parentId)->get();
        return $data;
    }

    /**
     * Validate Item in many-to-many form when create parent
     */
    public static function validateItem($data, $action, $id)
    {
        $msgValidate = [];
        $rule = [
            'name' => 'required',
            'price' => 'required|numeric'
        ];
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            $data['id'] = $id;
            return ['rs'=>System::SUCCESS, 'id'=>$id, 'itemData'=>json_encode($data)];
        }
    }

    /**
     * Delete item
     */
    public static function deleteItem($idArray)
    {
        try {
            self::whereIn('id', $idArray)->delete();
            return ['rs'=>System::SUCCESS];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }
}