<?php

namespace Modules\Frontend\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Attribute;
use Modules\Backend\Models\AttributeGroup;
use Modules\Backend\Models\AttributeProperty;
use Modules\Backend\Models\Label;
use Modules\Backend\Models\ProductToCategory;
use Modules\Backend\Models\ProductToProperty;
use Modules\Frontend\Classes\Frontend;
use Modules\Frontend\Classes\Price;

class Product extends Model
{
    /**
     * Get related product
     */
    public static function getRelatedProduct($id)
    {
        $cacheKey = 'related_product_'.$id;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($id) {
            $category = ProductToCategory::where('product_id', $id)
                ->first();
            $data = [];
            if (!empty($category)) {
                $categoryId = $category->category_id;
                $query = \Modules\Backend\Models\Product::where('id', '!=', $id);
                $query->whereHas('productToCategory', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
                $data = $query->limit(5)->get();
                if (!empty($data)) {
                    $data = Frontend::checkProductCanBuy($data);
                    $data = Price::addFinalPriceForProductObject($data);
                    $data = $data->toArray();
                }
            }
            return [
                'product' => $data,
                'allLabel' => Label::getAllProductLabel()
            ];
        });
        return $rs;
    }
}