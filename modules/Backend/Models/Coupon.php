<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Facades\CouponSave;
use Modules\Backend\Core\System;

class Coupon extends AppModel
{
    protected $table = 'coupon';

    const IS_FOR_ALL = 1;
    const IS_NOT_FOR_ALL = 0;

    const NOT_NEED_LOGGED_IN = 0;
    const NEED_LOGGED_IN = 1;

    const PERCENTAGE = 1;
    const FIXED_AMOUNT = 2;

    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'code', 'name'=>__('Backend.Lang::lang.field.code')],
            ['column'=>'discount', 'name'=>__('Backend.Lang::lang.field.discount')],
            ['column'=>'start_date', 'name'=>__('Backend.Lang::lang.field.start_date'),
                'partial'=>'Backend.View::share.startDate'],
            ['column'=>'end_date', 'name'=>__('Backend.Lang::lang.field.end_date'),
                'partial'=>'Backend.View::share.endDate'],
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
            $data = Coupon::find($id);
        }
        $type = Coupon::typeSelect();
        $arrayField = [
            ['text', 'code', [], System::YES],
            ['select', 'type', $type, System::YES],
            ['text', 'total', [], System::YES, [], '', __('Backend.Lang::lang.comment.total')],//decimal
            ['text', 'discount', [], System::YES, [], '', __('Backend.Lang::lang.comment.discount')],//decimal
            ['text', 'start_date', [], System::YES],
            ['text', 'end_date', [], System::YES],
            ['number', 'num_uses', [], System::NO, [], 0, __('Backend.Lang::lang.comment.num_uses')],
            ['number', 'num_per_customer', [], System::NO, [], 0, __('Backend.Lang::lang.comment.num_per_customer')],
            ['switch', 'status', [], System::NO, [], System::ENABLE],
            ['switch', 'logged', [], System::NO, [], Coupon::NEED_LOGGED_IN, __('Backend.Lang::lang.comment.logged')],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['template'] = 'Backend.View::group.coupon.form';
        $form['coupon_prefix'] = Config::getConfigByKeyInKeyConfig('coupon_prefix', System::COUPON_PREFIX_DEFAULT);
        $form['coupon_length_random'] = Config::getConfigByKeyInKeyConfig(
            'coupon_length_random',
            System::COUPON_LENGTH_RANDOM_DEFAULT
        );
        $categoryEdit = [];
        $productEdit = [];
        $categoryEditStr = '';
        $productEditStr = '';
        if (isset($data->id)) {
            $categoryEdit = CouponSave::getCategoryCoupon($data->id);
            $categoryEditStr = CouponSave::getIdOfProductOrCategory($categoryEdit);
            $productEdit = CouponSave::getProductCoupon($data->id);
            $productEditStr = CouponSave::getIdOfProductOrCategory($productEdit);
        }
        $form['categoryEdit'] = $categoryEdit;
        $form['productEdit'] = $productEdit;
        $form['categoryEditStr'] = $categoryEditStr;
        $form['productEditStr'] = $productEditStr;
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [];
        $rule = [
            'total' => 'required',
            'discount' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
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
            $id = CouponSave::saveCoupon($data);
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            $rs = ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
            return $rs;
        }
    }

    /**
     * type select
     */
    public static function typeSelect()
    {
        return [
            self::PERCENTAGE => __('Backend.Lang::lang.general.percentage'),
            self::FIXED_AMOUNT => __('Backend.Lang::lang.general.fix_amount'),
        ];
    }

    /**
     * Is for all select
     */
    public static function isNeedLoggedIn()
    {
        return [
            self::NOT_NEED_LOGGED_IN => __('Backend.Lang::lang.coupon.not_need_logged_in'),
            self::NEED_LOGGED_IN => __('Backend.Lang::lang.coupon.need_logged_in'),
        ];
    }

    public static function searchItem($post, $obj)
    {
        $keySearch = $post['q'];
        $data = $obj::select('id', 'name')
            ->where('name', 'like', '%'.$keySearch.'%')
            ->where('status', System::STATUS_ACTIVE)
            ->get();
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[] = [
                    'id' => $row->id,
                    'text' => $row->name
                ];
            }
        }
        return $rs;
    }

}