<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;

class OrderStatusChange extends AppModel
{
    protected $table = 'order_status_change';

    /**
     * belongs to order status
     */
    public function status()
    {
        return $this->belongsTo('\Modules\Backend\Models\OrderStatus', 'order_status_id', 'id');
    }

}