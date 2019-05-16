<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;

class CouponToCategory extends AppModel
{
    protected $table = 'coupon_to_category';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    /**
     * Belong to category
     */
    public function category()
    {
        return $this->belongsTo('Modules\Backend\Models\Category', 'category_id', 'id');
    }
}