<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;

class ProductToCategory extends AppModel
{
    protected $table = 'product_to_category';
    protected $fillable = ['product_id', 'category_id'];
    public $timestamps = false;

}