<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class Page extends AppModel
{
    protected $table = 'page';

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
            $data = Page::find($id);
        }
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['text', 'slug', [], System::YES],
            ['textarea', 'body', []],
            ['textarea', 'seo_title', []],
            ['textarea', 'seo_keyword', []],
            ['textarea', 'seo_description', []],
            ['switch', 'status', [], System::NO, [], System::ENABLE],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);

        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['template'] = 'Backend.View::group.page.form';
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
            'slug' => 'required|unique:page',
        ];
        if ($data['id'] != 0) {
            $rule['slug'] = 'required|unique:page,slug,'.$data['id'];
        }
        $routeType = System::ROUTES_TYPE_PAGE;
        return AppModel::validateSlugData($data, $rule, $msgValidate, $controller, $routeType, $close);
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        try {
            $id = $data['id'];
            $model = new Page();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->slug = $data['slug'];
            $model->body = $data['body'];
            $model->seo_title = $data['seo_title'];
            $model->seo_keyword = $data['seo_keyword'];
            $model->seo_description = $data['seo_description'];
            $model->status = isset($data['status']) ? $data['status'] : System::NO;
            $model->save();
            Routes::saveRoutes($data['id'], $data['slug'], $model->id, System::ROUTES_TYPE_PAGE);
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }
}