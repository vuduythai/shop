<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class Geo extends AppModel
{
    protected $table = 'geo_zone';
    public $timestamps = false;

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
            $data = Geo::find($id);
        }
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['textarea', 'description', []]
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
            $model = new Geo();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->description = $data['description'];
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Geo Select data
     */
    public static function geoSelect()
    {
        $rs[''] = __('Backend.Lang::lang.shipping.select_geo');
        $data = self::select('id', 'name')->get();
        foreach ($data as $row) {
            $rs[$row->id] = $row->name;
        }
        return $rs;
    }


}