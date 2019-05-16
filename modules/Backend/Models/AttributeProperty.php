<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;
use Illuminate\Support\Facades\Validator;

class AttributeProperty extends AppModel
{
    protected $table = 'attribute_property';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    public function attribute()
    {
        return $this->belongsTo('Modules\Backend\Models\Attribute', 'attribute_id', 'id');
    }

    /**
     * Get item by parent id
     */
    public static function getItemByParentId($parentId)
    {
        $data = self::where('attribute_id', $parentId)->get();
        return $data;
    }

    /**
     * Validate Item in many-to-many form when create parent
     */
    public static function validateItem($data, $action, $id)
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
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }
}