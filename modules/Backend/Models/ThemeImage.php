<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\System;
use Illuminate\Support\Facades\Validator;

class ThemeImage extends AppModel
{
    protected $table = 'theme_image';
    public $timestamps = false;

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
        $itemTable = with(new ThemeImage())->getTable();
        $relationTable = with(new ThemeToImage())->getTable();
        $data = DB::table($itemTable.' AS i')
            ->leftJoin($relationTable.' AS r', 'r.theme_image_id', '=', 'i.id')
            ->where('r.theme_id', $parentId)
            ->orderBy('r.sort_order', 'asc')
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
            ThemeToImage::whereIn('theme_image_id', $idArray)->delete();
            self::whereIn('id', $idArray)->delete();
            DB::commit();
            return ['rs'=>System::SUCCESS];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Save item
     */
    public static function saveItem($data, $action, $id)
    {
        $model = new ThemeImage();
        if ($action == System::ACTION_UPDATE) {
            $model = self::find($id);
        }
        $model->name = $data['name'];
        $model->image = $data['image'];
        $model->link = $data['link'];
        $model->title = $data['title'];
        $model->alt = $data['alt'];
        $model->description = $data['description'];
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
}