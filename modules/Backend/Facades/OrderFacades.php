<?php
/**
 * Handle order in admin
 */
namespace Modules\Backend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Models\Order;
use Modules\Backend\Models\OrderStatus;
use Modules\Backend\Models\OrderStatusChange;

class OrderFacades extends Model
{
    /**
     * Get order detail
     */
    public static function getOrderDetail($id)
    {
        $data = Order::with(
            [
                'product', 'statusChange.status', 'ship', 'payment', 'option'
            ]
        )
            ->where('id', $id)
            ->first();
        return $data;
    }

    /**
     * Get order status
     */
    public static function getOrderStatus()
    {
        $data = OrderStatus::get()->toArray();
        return $data;
    }

    /**
     * Change payment status
     */
    public static function changePaymentStatus($post)
    {
        $order = Order::find($post['id']);
        $order->payment_status = $post['payment_status'];
        $order->save();
    }

    /**
     * Update order status
     */
    public static function updateOrderStatus($id, $status)
    {
        $order = Order::find($id);
        $order->order_status_id = $status;
        $order->save();
    }


    /**
     * Change order status
     */
    public static function createOrderStatusChange($post)
    {
        DB::beginTransaction();
        try {
            $model = new OrderStatusChange();
            $model->order_id = $post['id'];
            $model->order_status_id = $post['order_status_id'];
            $model->comment = $post['comment'];
            $model->save();
            self::updateOrderStatus($post['id'], $post['order_status_id']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}