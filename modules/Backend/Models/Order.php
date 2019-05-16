<?php
namespace Modules\Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Backend\Core\System;

class Order extends Model
{
    public $table = 'order';

    const PAYMENT_METHOD_COD = 1;
    const PAYMENT_METHOD_PAYPAL = 2;
    const PAYMENT_METHOD_STRIPE = 3;

    const PAYMENT_STATUS_NOT_PAID = 0;
    const PAYMENT_STATUS_PAID = 1;

    /**
     * Has many product
     */
    public function product()
    {
        return $this->hasMany('\Modules\Backend\Models\OrderProduct', 'order_id', 'id');
    }

    /**
     * belongs to order status
     */
    public function orderStatus()
    {
        return $this->belongsTo('\Modules\Backend\Models\OrderStatus', 'order_status_id', 'id');
    }

    /**
     * Has many status change
     */
    public function statusChange()
    {
        return $this->hasMany('\Modules\Backend\Models\OrderStatusChange', 'order_id', 'id');
    }

    /**
     * Has many option
     */
    public function option()
    {
        return $this->hasMany('\Modules\Backend\Models\OrderOption', 'order_id', 'id');
    }

    /**
     * belongs to ship
     */
    public function ship()
    {
        return $this->belongsTo('\Modules\Backend\Models\Shipping', 'shipping_rule_id', 'id');
    }

    /**
     * belong to payment
     */
    public function payment()
    {
        return $this->belongsTo('\Modules\Backend\Models\Payment', 'payment_method', 'code');
    }

    /**
     * key 'relation' is a string: 'relation,field_name'
     */
    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('billing_email', 'like', '%'.$params['key'].'%');
            $query->orWhere('shipping_email', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'billing_email', 'name'=>__('Backend.Lang::lang.field.billing_email')],
            ['column'=>'shipping_email', 'name'=>__('Backend.Lang::lang.field.shipping_email')],
            ['column'=>'', 'name'=>__('Backend.Lang::lang.field.order_status_id'),
                'relation'=>'orderStatus,name,color'],
            ['column'=>'total', 'name'=>__('Backend.Lang::lang.field.total')],
            ['column'=>'created_at', 'name'=>__('Backend.Lang::lang.field.created_at'),
                'partial'=>'Backend.View::share.dateCreatedAt'],
        ];
        $rs = [
            'data' => $data,
            'field' => $field,
            'filter' => [

            ]
        ];
        return $rs;
    }

    /**
     * Get order detail
     */
    public static function getOrderDetail($id)
    {
        $data = self::select('*')
            ->where('id', $id)
            ->first();
        $rs = [];
        if (!empty($data)) {
            $rs = $data->toArray();
        }
        return $rs;
    }

    /**
     * payment method name
     */
    public static function paymentMethodName($id)
    {
        $arrayPayment = [
            self::PAYMENT_METHOD_COD => trans('Backend.Lang::lang.order.cash_on_delivery'),
            self::PAYMENT_METHOD_PAYPAL => trans('Backend.Lang::lang.order.paypal'),
            self::PAYMENT_METHOD_STRIPE => trans('Backend.Lang::lang.order.stripe'),
        ];
        $name = trans('Backend.Lang::lang.order.cash_on_delivery');
        if (array_key_exists($id, $arrayPayment)) {
            $name = $arrayPayment[$id];
        }
        return $name;
    }


}