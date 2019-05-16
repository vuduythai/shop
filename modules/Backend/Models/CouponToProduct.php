<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;

class CouponToProduct extends AppModel
{
    protected $table = 'coupon_to_product';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    /**
     * belong to product
     */
    public function product()
    {
        return $this->belongsTo('Modules\Backend\Models\Product', 'product_id', 'id');
    }
}