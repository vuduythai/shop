<?php
namespace Modules\Backend\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOption extends Model
{
    public $table = 'order_option';
    public $timestamps = false;//disable 'created_at' and 'updated_at'
}