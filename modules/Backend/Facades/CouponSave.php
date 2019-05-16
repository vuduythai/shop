<?php
/**
 * Save coupon data
 */
namespace Modules\Backend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Backend\Models\Category;
use Modules\Backend\Models\Coupon;
use Modules\Backend\Models\CouponToCategory;
use Modules\Backend\Models\CouponToProduct;
use Modules\Backend\Models\Product;
use Modules\Backend\Core\System;

class CouponSave extends Model
{
    /**
     * Search category and product for autocomplete select2
     */
    public static function searchItem($post, $obj)
    {
        $keySearch = $post['q'];
        $data = $obj::select('id', 'name')
            ->where('name', 'like', '%'.$keySearch.'%')
            ->where('status', System::ENABLE)
            ->get();
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[] = [
                    'id' => $row->id,
                    'text' => $row->name
                ];
            }
        }
        return $rs;
    }

    /**
     * Search category
     */
    public static function searchCategory($post)
    {
        $obj = new Category();
        return self::searchItem($post, $obj);
    }

    /**
     * Search product
     */
    public static function searchProduct($post)
    {
        $obj = new Product();
        return self::searchItem($post, $obj);
    }

    /**
     * Save product coupon or category coupon
     * $obj: instance of product or category
     * $field : field that assign data 'category_id' or 'product_id' in table
     * $id: to know create or update $post['id']
     * $itemArray : data of category or product
     * $idCouponSaved: id of coupon that saved
     */
    public static function saveProductOrCategoryCoupon($obj, $field, $id, $itemArray, $idCouponSaved)
    {
        $data = [];
        foreach ($itemArray as $row) {
            $data[] = [
                'coupon_id' => $idCouponSaved,
                $field => $row
            ];
        }
        if ($id != 0) {//update
            $obj::where('coupon_id', $idCouponSaved)->delete();
        }
        if (!empty($data)) {
            $obj::insert($data);
        }
    }

    /**
     * Save product coupon
     */
    public static function saveProductCoupon($post, $idCouponSaved)
    {
        $obj = new CouponToProduct();
        !empty($post['product']) ? $data = $post['product'] : $data = [];
        self::saveProductOrCategoryCoupon($obj, 'product_id', $post['id'], $data, $idCouponSaved);
    }

    /**
     * Save category coupon
     */
    public static function saveCategoryCoupon($post, $idCouponSaved)
    {
        $obj = new CouponToCategory();
        !empty($post['category']) ? $data = $post['category'] : $data = [];
        self::saveProductOrCategoryCoupon($obj, 'category_id', $post['id'], $data, $idCouponSaved);
    }

    /**
     * Apply for all category and all product
     */
    public static function applyForAll($id)
    {
        Coupon::where('id', $id)->update(['is_for_all'=>Coupon::IS_FOR_ALL]);
    }

    /**
     * After save: save product and category
     */
    public static function saveCategoryAndProductForCoupon($post, $idCouponSaved)
    {
        if (empty($post['category']) && empty($post['product'])) {
            self::applyForAll($idCouponSaved);
        } else {
            self::saveCategoryCoupon($post, $idCouponSaved);
            self::saveProductCoupon($post, $idCouponSaved);
            Coupon::where('id', $idCouponSaved)->update(['is_for_all'=>Coupon::IS_NOT_FOR_ALL]);
        }
    }

    /**
     * Save coupon
     */
    public static function saveCoupon($data)
    {
        $id = $data['id'];
        $model = new Coupon();
        if ($id != 0) {//edit
            $model = Coupon::find($id);
        }
        $model->code = $data['code'];
        $model->type = $data['type'];
        $model->logged = isset($data['logged']) ? $data['logged'] : System::NO;
        $model->discount = $data['discount'];
        $model->total = $data['total'];
        $model->start_date = $data['start_date'];
        $model->end_date = $data['end_date'];
        $model->num_uses = $data['num_uses'];
        $model->num_per_customer = $data['num_per_customer'];
        $model->status = !empty($data['status']) ? $data['status'] : System::STATUS_UNACTIVE;
        $model->save();
        self::saveCategoryAndProductForCoupon($data, $model->id);
        return $model->id;
    }

    /**
     * FOR EDIT COUPON
     */

    /**
     * Get category or product id
     */
    public static function getIdOfProductOrCategory($data)
    {
        $rs = [];
        $str = '';
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[] = $row['id'];
            }
            $str = implode(',', $rs);
        }
        return $str;
    }

    /**
     * Convert category or product id
     */
    public static function convertCatOrProductId($field, $data, $relation)
    {
        $convert = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $convert[] = [
                    'id' => $row[$field],
                    'name' => $row->$relation->name
                ];
            }
        }
        return $convert;
    }

    /**
     * Get category coupon for edit
     */
    public static function getCategoryCoupon($id)
    {
        $data = CouponToCategory::where('coupon_id', $id)->with('category:id,name')->get();
        $convert = self::convertCatOrProductId('category_id', $data, 'category');
        return $convert;
    }

    /**
     * Get product coupon for edit
     */
    public static function getProductCoupon($id)
    {
        $data = CouponToProduct::where('coupon_id', $id)->with('product:id,name')->get();
        $convert = self::convertCatOrProductId('product_id', $data, 'product');
        return $convert;
    }

    /**
     * END FOR EDIT COUPON
     */


}
