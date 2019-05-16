<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class Review extends AppModel
{
    protected $table = 'reviews';

    /**
     * key 'relation' is a string: 'relation,field_name'
     */
    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('author', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'author', 'name'=>__('Backend.Lang::lang.review.author')],
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
            $data = Review::find($id);
        }
        $arrayField = [
            ['text', 'author', [], System::YES],
            ['textarea', 'content', [], System::YES],
            ['number', 'rate', [], System::YES],
            ['switch', 'status', [], System::NO, [], System::ENABLE]
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
            'author' => 'required'
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
            $model = new Review();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->author = $data['author'];
            $model->content = $data['content'];
            $model->rate = $data['rate'];
            $model->status = isset($data['status']) ? $data['status'] : System::DISABLE;
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }


}