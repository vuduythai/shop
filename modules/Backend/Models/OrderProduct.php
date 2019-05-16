<?php
namespace Modules\Backend\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public $table = 'order_product';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    /**
     * Belong to product
     */
    public function product()
    {
        return $this->belongsTo('Modules\Backend\Models\Product', 'product_id', 'id');
    }

    /**
     * has one downloadable link
     */
    public function virtual()
    {
        return $this->hasOne('Modules\Backend\Models\ProductVirtual', 'product_id', 'product_id');
    }

    /**
     * belong to order
     */
    public function order()
    {
        return $this->belongsTo('Modules\Backend\Models\Order', 'order_id', 'id');
    }

    /**
     * Get product for order
     */
    public static function getOrderProduct($id)
    {
        $data = self::where('order_id', $id)
            ->with(['product:id,is_virtual_product', 'virtual:product_id,sample,link'])
            ->get()
            ->toArray();
        return $data;
    }
}