<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class Theme extends AppModel
{
    protected $table = 'theme';
    public $timestamps = false;


    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name'), 'sort'=>'no-sort'],
            ['column'=>'slug', 'name'=>__('Backend.Lang::lang.field.slug'), 'sort'=>'no-sort'],
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
            ['text', 'slug', [], System::YES],
            ['textarea', 'description', []],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['image'] = [];
        if ($id != '') {
            $form['image'] = '';
        }
        $form['template'] = 'Backend.View::group.theme.form';
        $form['itemName'] = 'image';
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
            'slug' => 'required|unique:theme'
        ];
        if ($data['id'] != 0) {//update
            $rule['slug'] = 'required|unique:theme,slug,'.$data['id'];
        }
        //convert message when validate array
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            return self::saveRecord($data, $close);
        }
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        DB::beginTransaction();
        try {
            $model = new Theme();
            if ($data['id'] != 0) {
                $model = Theme::find($data['id']);
            }
            $model->name = $data['name'];
            $model->slug = $data['slug'];
            $model->description = $data['description'];
            $model->save();
            $id = $model->id;
            if (isset($data['items'])) {
                $itemId = $data['items'];
                $itemIdArray = explode(';', $itemId);
                $itemToParent = [];
                $i = 1;
                foreach ($itemIdArray as $row) {
                    $itemToParent[] = [
                        'theme_id' => $id,
                        'theme_image_id' => $row,
                        'sort_order' => $i
                    ];
                    $i++;
                }
                ThemeToImage::where('theme_id', $id)->delete();
                if (!empty($itemToParent)) {
                    ThemeToImage::insert($itemToParent);
                }
            } else {
                ThemeToImage::where('theme_id', $id)->delete();
            }
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            $rs = ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
            return $rs;
        }
    }

   /**
     * Delete theme
     */
    public static function deleteData($idArray)
    {
        foreach ($idArray as $row) {
            self::where('id', $row)->delete();
        }
    }

}