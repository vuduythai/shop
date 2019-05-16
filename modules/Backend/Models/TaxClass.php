<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class TaxClass extends AppModel
{
    protected $table = 'tax_class';
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
        $taxRule = [];
        if ($id != '') {//edit
            $data = self::find($id);
            $taxRuleArray = TaxRule::select('tax_rate_id')->where('tax_class_id', $id)->get();
            if (!empty($taxRuleArray)) {
                foreach ($taxRuleArray as $row) {
                    $taxRule[] = $row->tax_rate_id;
                }
            }
        }
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['textarea', 'description', []]
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['template'] = 'Backend.View::group.tax_class.form';
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['itemName'] = 'tax_rate';
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [
        ];
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
        DB::beginTransaction();
        try {
            $id = $data['id'];
            $model = new TaxClass();
            if ($id != 0) {//edit
                $model = self::find($id);
                TaxRule::where('tax_class_id', $id)->delete();
            }
            $model->name = $data['name'];
            $model->description = $data['description'];
            $model->save();
            $id = $model->id;
            if (isset($data['items'])) {
                $itemId = $data['items'];
                $itemIdArray = explode(';', $itemId);
                $itemToParent = [];
                foreach ($itemIdArray as $row) {
                    $itemToParent[] = [
                        'tax_class_id' => $id,
                        'tax_rate_id' => $row,
                    ];
                }
                TaxRule::where('tax_class_id', $id)->delete();
                if (!empty($itemToParent)) {
                    TaxRule::insert($itemToParent);
                }
            } else {
                TaxRule::where('theme_id', $id)->delete();
            }
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            $rs = ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
            return $rs;
        }
    }

    /**
     * Get select tax class
     */
    public static function taxClassSelect()
    {
        $data = self::select('id', 'name')
            ->get();
        $data = Functions::convertArrayKeyValue($data, 'id', 'name');
        return $data;
    }

}