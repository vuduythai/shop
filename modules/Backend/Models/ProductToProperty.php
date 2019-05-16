<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;

class ProductToProperty extends AppModel
{
    protected $table = 'product_to_property';
    public $timestamps = false;//disable 'created_at' and 'updated_at'

    /**
     * Belong to attribute
     */
    public function attribute()
    {
        return $this->belongsTo('Modules\Backend\Models\Attribute');
    }
}