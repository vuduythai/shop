<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\System;

class MailTemplate extends AppModel
{
    protected $table = 'mail_template';

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

//    /**
//     * Get content of file then save to db
//     */
//    public static function getContentOfFile($fileName)
//    {
//        $content = file_get_contents(base_path().'/Themes/base/views/mails/'.$fileName.'.twig');
//        $model = new MailTemplate();
//        $model->name = $fileName;
//        $model->mail_content = $content;
//        $model->save();
//    }

    /**
     * Return form to create and edit
     */
    public static function formCreate($request, $controller, $id = '')
    {
        $data = new \stdClass();
        if ($id != '') {//edit
            $data = MailTemplate::find($id);
        }
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['textarea', 'mail_content', []]
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['template'] = 'Backend.View::group.mail.form';
        $mailCss = file_get_contents(base_path().'/Themes/'.env('THEME_NAME', 'base').'/assets/css/mail.css');
        $form['mailCss'] = '<style>'.$mailCss.'</style>';
        $mailContent = $form['mail_content']['value'];
        $mailContent = str_replace('{% extends "mails.layout" %}', '', $mailContent);
        $mailContent = str_replace('{% block content %}', '', $mailContent);
        $mailContent = str_replace('{% endblock %}', '', $mailContent);
        $form['mailContentPreview'] = $mailContent;
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
            $model = new MailTemplate();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->mail_content = $data['mail_content'];
            $model->save();
            $file = '/Themes/'.env('THEME_NAME', 'base').'/views/mails/'.$data['name'].'.twig';
            $file_handle = fopen(base_path().$file, "w+");
            fwrite($file_handle, $data['mail_content']);
            fclose($file_handle);
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

}