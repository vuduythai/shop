<?php

namespace Modules\Frontend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Attribute;
use Modules\Backend\Models\AttributeGroup;
use Modules\Backend\Models\AttributeProperty;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Currency;
use Modules\Backend\Models\Option;
use Modules\Backend\Models\OptionValue;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\ProductToOption;
use Modules\Backend\Models\ProductToProperty;
use Modules\Backend\Models\ProductVariant;
use Modules\Backend\Models\Review;
use Modules\Frontend\Classes\Frontend;
use Modules\Frontend\Classes\Price;

class ProductFacades extends Model
{

    /**
     * Get filter for product detail
     */
    public static function getAttributeForProductDetail($propertyArray, $propertyNext)
    {
        $data = AttributeProperty::with(['attribute'])->whereIn('id', $propertyArray)->get();
        $rs = [];
        foreach ($data as $row) {
            $row->property_next = array_key_exists($row->id, $propertyNext) ? $propertyNext[$row->id] : '';
            $rs[$row->attribute->id]['attribute_name'] = $row->attribute->name;
            $rs[$row->attribute->id]['property'][] = Functions::objectToArray($row);
        }
        return $rs;
    }

    /**
     * Convert filter option of product child
     * for example, $property variable :
     *   $property[0] = [1, 5, 6];
     *   $property[1] = [2, 7, 8];
     *   $property[2] = [3];
     * $numMax = 3
     */
    public static function convertPropertyOfProductChild($data)
    {
        $property = [];
        foreach ($data as $row) {
            $propertyString = $row['property_string'];
            $property[] = explode(System::SEPARATE, $propertyString);
        }
        //find what num max
        $num = [];
        foreach ($property as $row) {
            $num[] = count($row);
        }
        $numMax = max($num);
        $rs = [];
        for ($i = 0; $i<$numMax-1; $i++) {
            foreach ($property as $row) {
                if (isset($row[$i])) {
                    $rs[$row[$i]][] = isset($row[$i+1]) ? $row[$i+1] : '';
                }
            }
        }
        $rsConvert = [];
        foreach ($rs as $key => $value) {
            $rsConvert[$key] = implode(System::SEPARATE, $value);
        }
        return $rsConvert;
    }

    /**
     * Get configurable product data
     */
    public static function getVariant($parent, $id)
    {
        $childProduct = ProductVariant::where('product_id', $id)
            ->get()
            ->toArray();
        $propertyNext = self::convertPropertyOfProductChild($childProduct);
        $propertyIdArray = [];
        foreach ($childProduct as $row) {
            $property = explode(System::SEPARATE, $row['property_string']);
            foreach ($property as $p) {
                $propertyIdArray[] = $p;
            }
        }
        $propertyIdArrayUnique = array_unique($propertyIdArray);
        $filter = self::getAttributeForProductDetail(array_values($propertyIdArrayUnique), $propertyNext);
        $childConvert = [];
        foreach ($childProduct as $row) {
            $childConvert[$row['property_string']] = $row;
        }
        $rs = [
            'child' => $childProduct,
            'filter' => $filter,
            'childConvert' => $childConvert
        ];
        return $rs;
    }

    /**
     * Get constant
     */
    public static function getConstantForProductDetail()
    {
        $data['attribute_type_color'] = Attribute::TYPE_COLOR;
        $data['attribute_type_text'] = Attribute::TYPE_TEXT;
        $data['in_stock'] = System::IN_STOCK;
        $data['is_out_of_stock'] = System::OUT_OF_STOCK;
        $data['type_product_configurable'] = System::PRODUCT_TYPE_CONFIGURABLE;
        $data['enable_review'] = Config::getConfigByKeyInKeyConfigCache('enable_review', System::DISABLE);
        $data['enable'] = System::ENABLE;
        $data['yes'] = System::YES;
        $data['separate'] = System::SEPARATE;
        $data['option_type_select'] = Option::TYPE_SELECT;
        $data['option_type_radio'] = Option::TYPE_RADIO;
        $data['option_type_checkbox'] = Option::TYPE_CHECKBOX;
        $data['option_type_multiselect'] = Option::TYPE_MULTI_SELECT;
        $data['fixed_amount'] = System::TYPE_FIX_AMOUNT;
        $data['symbol_position_before'] = Currency::POSITION_BEFORE;
        return $data;
    }

    /**
     * Convert option
     */
    public static function convertOption($data)
    {
        $rs = [];
        foreach ($data as $row) {
            $rs[$row->option_id]['id'] = $row->option_id;
            $rs[$row->option_id]['name'] = $row->option_name;
            $rs[$row->option_id]['type'] = $row->option_type;
            $rs[$row->option_id]['value'][] = Functions::objectToArray($row);
        }
        return $rs;
    }

    /**
     * Get option
     */
    public static function getOption($productId)
    {
        $optionTable = with(new Option())->getTable();
        $optionValueTable = with(new OptionValue())->getTable();
        $productToOptionTable = with(new ProductToOption())->getTable();
        $data = DB::table($optionTable.' AS o')
            ->select('o.name AS option_name', 'ov.name AS value_name', 'pto.*')
            ->leftJoin($productToOptionTable.' AS pto', 'pto.option_id', '=', 'o.id')
            ->leftJoin($optionValueTable.' AS ov', 'pto.value_id', '=', 'ov.id')
            ->where('pto.product_id', $productId)
            ->get()
            ->toArray();
        $rs = [];
        if (!empty($data)) {
            $rs = self::convertOption($data);
        }
        return $rs;
    }

    /**
     * Convert option, value, value type
     */
    public static function convertOptionValueType($option)
    {
        $rs = [];
        foreach ($option as $row) {
            $valueType = [];
            foreach ($row['value'] as $v) {
                $valueType[$v['value_id']] = [
                    'value_type' => $v['value_type'],
                    'value_price' => $v['value_price'],
                    'value_name' => $v['value_name'],
                    'value_id' => $v['value_id'],
                    'option_name' => $v['option_name'],
                    'option_id' => $v['option_id'],
                    'option_type' => $v['option_type'],
                    'product_id' => $v['product_id'],
                ];
            }
            $rs[$row['id']] = $valueType;
        }
        return $rs;
    }

    /**
     * Get review product
     */
    public static function getProductReview($id, $page)
    {
        $data = Review::where('product_id', $id)
            ->where('status', System::STATUS_ACTIVE)
            ->paginate(System::PAGE_SIZE_DEFAULT, ['*'], 'page', $page);
        $data = Functions::objectToArray($data);
        return $data;
    }

    /**
     * Convert attribute product
     */
    public static function convertAttributeProduct($data)
    {
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[$row['attr_group_id']]['name'] = $row['attr_group_name'];
                if ($row['property_id'] == 0) {
                    $rs[$row['attr_group_id']]['attribute'][$row['attribute_id']] = [
                        'name'=>$row['attr_name'], 'value'=>$row['value']
                    ];
                } else {
                    $rs[$row['attr_group_id']]['attribute'][$row['attribute_id']]['name'] = $row['attr_name'];
                    $rs[$row['attr_group_id']]['attribute'][$row['attribute_id']]['value'][] = $row['property_name'];
                }
            }
        }
        $rsConvert = [];
        foreach ($rs as $key => $value) {
            $rsConvert[$key]['name'] = $rs[$key]['name'];
            foreach ($rs[$key]['attribute'] as $attributeId => $attributeValue) {
                $valueFinal = $attributeValue['value'];
                if (is_array($attributeValue['value'])) {
                    $valueFinal = implode(',', $attributeValue['value']);
                }
                $rsConvert[$key]['attribute'][$attributeId]['name'] = $attributeValue['name'];
                $rsConvert[$key]['attribute'][$attributeId]['value'] = $valueFinal;
            }
        }
        return $rsConvert;
    }

    /**
     * Get attribute product
     */
    public static function getAttributeProduct($id)
    {
        $attributeGroupTable = with(new AttributeGroup())->getTable();
        $attributeTable = with(new Attribute())->getTable();
        $propertyTable = with(new AttributeProperty())->getTable();
        $productToProperty = with(new ProductToProperty())->getTable();
        $arraySelect = [
            'p.*', 'a.name AS attr_name', 'ag.name AS attr_group_name', 'ag.id AS attr_group_id',
            'pro.name AS property_name'
        ];
        $data = DB::table($productToProperty.' AS p')
            ->select($arraySelect)
            ->leftJoin($attributeTable.' AS a', 'a.id', '=', 'p.attribute_id')
            ->leftJoin($propertyTable.' AS pro', 'pro.id', '=', 'p.property_id')
            ->leftJoin($attributeGroupTable.' AS ag', 'ag.id', '=', 'a.attribute_group_id')
            ->where('a.is_display', Attribute::IS_FOR_DISPLAY)
            ->where('p.product_id', $id)
            ->orderBy('p.sort_order', 'asc')
            ->get();
        $data = Functions::objectToArray($data);
        $rs = self::convertAttributeProduct($data);
        return $rs;
    }

    /**
     * get product data
     */
    public static function loadProductCache($id)
    {
        $data = Product::where('id', $id)
            ->first();
        $galleryArray = [];
        $variant = [];
        $option = [];
        $valueType = [];
        $data = Price::addFinalPriceForDetailProduct($data);
        if (!empty($data)) {
            $data = $data->toArray();
            $gallery = $data['gallery'];
            $galleryArray = [];
            if ($gallery != '') {
                $galleryArray = explode(System::SEPARATE, $gallery);
            }
            $productType = $data['product_type'];
            $variant = [];
            if ($productType == System::PRODUCT_TYPE_CONFIGURABLE) {
                $variant = self::getVariant($data, $data['id']);
            }
            if ($data['is_has_option'] == System::ENABLE) {
                $option = self::getOption($data['id']);
                if (!empty($option)) {
                    $valueType = self::convertOptionValueType($option);
                }
            }
        }
        $breadCrumb = [
            'type' => System::ROUTES_TYPE_PRODUCT,
            'data' => Frontend::getBreadCrumb($data['category_default']),
            'name' => $data['name']
        ];
        $seo = [
            'seo_title' => $data['seo_title'] ? $data['seo_title'] : '',
            'seo_keyword' => $data['seo_keyword'] ? $data['seo_keyword'] : '',
            'seo_description' => $data['seo_description'] ? $data['seo_description'] : ''
        ];
        $currencyDefault = Config::getConfigByKeyInKeyConfigCache('currency_default', 1);
        $currencyData = Currency::getCurrencyById($currencyDefault);
        $rs = [
            'product' => $data,
            'gallery' => $galleryArray,
            'variant' => $variant,
            'option' => $option,
            'attribute' => self::getAttributeProduct($id),
            'review' => self::getProductReview($id, 1),
            'valueType' => $valueType,
            'const' => self::getConstantForProductDetail(),
            'breadcrumbArray' => $breadCrumb,
            'seo' => $seo,
            'currency' => $currencyData,
            'review_permission' => Config::getConfigByKeyInKeyConfigCache('review_allow_customer_create', System::NO)
        ];
        return $rs;
    }

    /**
     * Get product detail cache
     */
    public static function loadProduct($id)
    {
        $cacheKey = 'product_'.$id;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($id) {
            return ProductFacades::loadProductCache($id);
        });
        $rs['product'] = Frontend::checkSingleProductCanBuyArray($rs['product']);
        return $rs;
    }

}