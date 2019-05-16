<?php
/**
 * To create and update product
 */
namespace Modules\Backend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\Functions;
use Modules\Backend\Models\Attribute;
use Modules\Backend\Models\AttributeSet;
use Modules\Backend\Models\AttributeProperty;
use Modules\Backend\Models\Option;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\ProductToCategory;
use Modules\Backend\Models\ProductToOption;
use Modules\Backend\Models\ProductToProperty;
use Modules\Backend\Models\ProductVariant;
use Modules\Backend\Models\Routes;
use Modules\Backend\Core\System;
use Modules\Backend\Models\OptionValue;

class ProductFacades extends Model
{
    /**
     * Get product option value by option id
     */
    public static function getValueByOptionId($optionId)
    {
        $optionValue = OptionValue::where('option_id', $optionId)->get();
        $optionValueSelect = Functions::convertArrayKeyValue($optionValue, 'id', 'name');
        return [
            'optionValue' => $optionValue,
            'optionValueSelect' => $optionValueSelect
        ];
    }

    /**
     * Get property by attribute id
     */
    public static function getPropertyByAttributeId($attributeId)
    {
        $property = AttributeProperty::where('attribute_id', $attributeId)->get();
        return $property;
    }


    /**
     * Search property to create variant
     */
    public static function searchProperty($post)
    {
        $keySearch = $post['q'];
        $attribute = Attribute::select('id', 'name')
            ->where('name', 'like', '%'.$keySearch.'%')
            ->first();
        $rs = [];
        if (!empty($attribute)) {
            $attributeId = $attribute->id;
            $attributeName = $attribute->name;
            $property = self::getPropertyByAttributeId($attributeId);
            if (!empty($property)) {
                foreach ($property as $row) {
                    $rs[] = [
                        'id' => $attributeId.System::SEPARATE.$row->id,
                        'text' => $attributeName.': '.$row->name
                    ];
                }
            }
        }
        return $rs;
    }

    /**
     * Convert attribute property
     */
    public static function convertAttributeProperty($data, $attributeArray)
    {
        $rs = [];
        foreach ($data as $row) {
            $rs[$row->attribute->id]['attribute'] = [
                'name' => $row->attribute->name,
                'id' => $row->attribute->id,
                'is_filter' => $row->attribute->is_filter,
                'type' => $row->attribute->type
            ];
            if (!empty($row->name)) {
                $rs[$row->attribute->id]['property'][$row->id] = $row->name;
            }
        }
        $rsReSort = [];
        //re-sort attribute
        foreach ($attributeArray as $row) {
            foreach ($rs as $key => $value) {
                if ($row == $key) {
                    $rsReSort[$key] = $value;
                }
            }
        }
        return $rsReSort;
    }

    /**
     * Get attribute
     */
    public static function getAttributeProperty($attributeSetDefault)
    {
        $attributeSet = AttributeSet::select('attribute_json')
            ->where('id', $attributeSetDefault)->first();
        if (!empty($attributeSet)) {
            $attributeJson = $attributeSet->attribute_json;
            if (!empty($attributeJson)) {
                $attributeArray = json_decode($attributeJson, true);
                $data = AttributeProperty::with(['attribute'])
                    ->whereIn('attribute_id', $attributeArray)
                    ->get();
                $data = self::convertAttributeProperty($data, $attributeArray);
                return $data;
            }
        }
    }

    /**
     * Convert option data
     */
    public static function convertOptionData($post, $optionIdArray, $productSaved)
    {
        $data = [];
        $dataUpdate = [];
        $dataPlus = [];
        foreach ($optionIdArray as $key => $value) {
            $optionId = $key;
            $optionType = $value['option_type'];
            if (isset($value['value'])) {
                $value = $value['value'];
                foreach ($value as $k => $v) {
                    $array = [
                        'product_id' => $productSaved->id,
                        'option_id' => $optionId,
                        'option_type' => $optionType,
                        'value_id' => $v['id'],
                        'value_type' => $v['type'],
                        'value_price' => $v['price'],
                        'sort_order' => $v['sort_order']
                    ];
                    if ($v['id_update'] == 0) {
                        $data[] = $array;
                    } else {
                        $dataUpdate[$v['id_update']] = $array;
                    }
                }
            }
        }
        if (isset($post['option_id_plus'])) {
            foreach ($post['option_id_plus'] as $k => $v) {
                for ($i=0; $i<count($v['option_id']); $i++) {
                    $dataPlus[] = [
                        'product_id' => $productSaved->id,
                        'option_id' => $v['option_id'][$i],
                        'option_type' => $v['option_type'][$i],
                        'value_id' => $v['id'][$i],
                        'value_type' => $v['type'][$i],
                        'value_price' => $v['price'][$i],
                        'sort_order' => $v['sort_order'][$i]
                    ];
                }
            }
        }
        return [
            'data' => $data,
            'dataUpdate' => $dataUpdate,
            'dataPlus' => $dataPlus
        ];
    }

    /**
     * Save option
     */
    public static function saveOption($post, $productSaved)
    {
        $optionIdArray = $post['option_id'];
        $model = new ProductToOption();
        $optionConvert = self::convertOptionData($post, $optionIdArray, $productSaved);
        $data = $optionConvert['data'];
        $dataUpdate = $optionConvert['dataUpdate'];
        $dataPlus = $optionConvert['dataPlus'];
        if ($post['id'] != 0) {//update
            if (isset($post['product_to_option_delete_id'])) {
                ProductToOption::whereIn('id', $post['product_to_option_delete_id'])->delete();
            }
            if (!empty($post['product_to_option_id_update'])) {
                foreach ($post['product_to_option_id_update'] as $row) {
                    if (array_key_exists($row, $dataUpdate)) {
                        ProductToOption::where('id', $row)
                            ->update($dataUpdate[$row]);
                    }
                }
            }
        }
        $model->insert($data);
        if (isset($post['option_id_plus'])) {
            $model->insert($dataPlus);
        }
    }

    /**
     * Save product dynamic attribute
     */
    public static function saveProperty($post, $productSavedId, $id)
    {
        $i = 0;
        $data = [];
        foreach ($post['attribute_id'] as $attributeId) {
            if (array_key_exists($attributeId, $post['property'])) {
                $value = null;
                if (!is_array($post['property'][$attributeId])) {
                    if (array_key_exists($attributeId, $post['property'])) {
                        $value = $post['property'][$attributeId];
                    }
                    //attribute with value is customize text
                    $data[] = [
                        'product_id' => $productSavedId,
                        'attribute_id' => $attributeId,
                        'property_id' => 0,
                        'sort_order' => $post['attr_sort_order'][$i],
                        'value' => $value
                    ];
                } else {
                    //if has many property per attribute
                    foreach ($post['property'][$attributeId] as $p) {
                        $data[] = [
                            'product_id' => $productSavedId,
                            'attribute_id' => $attributeId,
                            'property_id' => $p,
                            'sort_order' => $post['attr_sort_order'][$i],
                            'value' => $value
                        ];
                    }
                }
            }
            $i++;
        }
        if ($id != 0) {//update, delete first
            ProductToProperty::where('product_id', $productSavedId)->delete();
        }
        if (!empty($data)) {//then insert
            ProductToProperty::insert($data);
        }
    }


    /**
     * Save category
     */
    public static function saveCategory($id, $productId, $category)
    {
        $data = [];
        foreach ($category as $row) {
            $data[] = [
                'product_id' => $productId,
                'category_id' => $row
            ];
        }
        if ($id != 0) {
            ProductToCategory::where('product_id', $productId)->delete();
        }
        ProductToCategory::insert($data);
    }

    /**
     * Save product label
     */
    public static function saveProductLabel($productId, $productLabel)
    {
        $data = '';
        if (!empty($productLabel)) {
            foreach ($productLabel as $row) {
                $data .= $row.System::SEPARATE;
            }
            $data = substr($data, 0, -1);
        }
        Product::where('id', $productId)->update(['product_label'=>$data]);
    }

    /**
     * Save product
     */
    public static function saveProductData($post)
    {
        $id = $post['id'];
        $model = new Product();
        if ($id != 0) {//create
            $model = Product::find($id);
        }
        $model->name = $post['name'];
        $model->slug = $post['slug'];
        $model->price = $post['price'];
        $model->price_promotion = $post['price_promotion'];
        $model->price_promo_from = $post['price_promo_from'];
        $model->price_promo_to = $post['price_promo_to'];
        $model->sku = $post['sku'];
        $model->is_in_stock = isset($post['is_in_stock']) ? $post['is_in_stock'] : System::DISABLE;
        $model->qty = $post['qty'];
        if ($id == 0) {
            $model->qty_order = 0;
        }
        $model->image = $post['image'];
        $model->sort_order = $post['sort_order'];
        $model->product_type = isset($post['variant']) ?
            System::PRODUCT_TYPE_CONFIGURABLE : System::PRODUCT_TYPE_SIMPLE;
        $model->is_variant_change_image = $post['is_variant_change_image'];
        $model->is_has_option = isset($post['option_id']) ? System::ENABLE : System::DISABLE;
        $model->tax_class_id = $post['tax_class_id'];
        $model->weight = $post['weight'];
        $model->weight_id = $post['weight_id'];
        $model->status = $post['status'];
        $model->category_default = $post['category_default'];
        $model->brand_id = $post['brand_id'];
        $model->short_intro = $post['short_intro'];
        $model->full_intro = $post['full_intro'];
        $model->tag = $post['tag'];
        $model->seo_title = $post['seo_title'];
        $model->seo_keyword = $post['seo_keyword'];
        $model->seo_description = $post['seo_description'];
        $model->is_featured_product = isset($post['is_featured_product']) ?
            $post['is_featured_product'] : System::DISABLE;
        $model->is_new = isset($post['is_new']) ? $post['is_new'] : System::DISABLE;
        $model->is_bestseller = isset($post['is_bestseller']) ? $post['is_bestseller'] : System::DISABLE;
        $model->is_on_sale = isset($post['is_on_sale']) ? $post['is_on_sale'] : System::DISABLE;
        $model->length_id = $post['length_id'];
        $model->length = $post['length'];
        $model->width = $post['width'];
        $model->height = $post['height'];
        $model->save();
        return $model;
    }

    /**
     * Save all data of product : product, attribute, category, option, routes
     */
    public static function saveAllDataOfProduct($post, $close)
    {
        DB::beginTransaction();
        try {
            $id = $post['id'];//to know if create or update
            $productSaved = self::saveProductData($post);
            $productId = $productSaved->id;
            //save gallery
            $gallery = '';
            if (!empty($post['product_gallery'])) {
                $gallery = implode(System::SEPARATE, $post['product_gallery']);
            }
            Product::where('id', $productId)->update(['gallery'=>$gallery]);

            //save product_label
            if (!empty($post['product_label'])) {
                self::saveProductLabel($productId, $post['product_label']);
            } else {
                if ($id != 0) {//just do when update
                    Product::where('id', $productId)->update(['product_label'=>'']);
                }
            }

            //save category
            self::saveCategory($id, $productId, $post['category']);

            //save variant
            if (!empty($post['variant'])) {
                self::saveVariant($post, $productSaved);
            } else {
                ProductVariant::where('product_id', $productId)->delete();
            }

            //save property
            if (!empty($post['property'])) {
                self::saveProperty($post, $productId, $id);
            }

            //Save option
            if (!empty($post['option_id'])) {
                self::saveOption($post, $productSaved);
            } else {
                ProductToOption::where('product_id', $productId)->delete();
            }

            //save routes
            $typeRoute = Routes::ROUTE_PRODUCT;
            Routes::saveRoutes($id, $post['slug'], $productId, $typeRoute);

            DB::commit();
            return ['rs'=>System::SUCCESS, 'id'=>$productId, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

    /**
     * VARIANT
     */

    /**
     * Insert product child
     * return product child id
     */
    public static function saveVariantData($parent, $post, $row)
    {
        $model = new ProductVariant();//create
        if ($row['id_update'] != 0) {//update
            $model = ProductVariant::find($row['id_update']);
        }
        $model->product_id = $parent->id;
        $model->qty_variant = $row['qty_variant'] != '' ? $row['qty_variant'] : $post['qty'];
        if ($row['id_update'] == 0) {//create
            $model->qty_order = 0;
        }
        $model->price_variant = $row['price_variant'] != '' ? $row['price_variant'] : 0;
        $model->variant_image = $row['variant_image'];
        $model->variant_gallery = !empty($row['variant_gallery']) ?
            $row['variant_gallery'] : '';
        $model->property_string = $row['property_string'];
        $model->save();
    }


    /**
     * Delete variant
     */
    public static function deleteVariant($arrayIdDelete)
    {
        ProductVariant::whereIn('id', $arrayIdDelete)->delete();
    }

    /**
     * Get id to delete
     */
    public static function getOldIdToDelete($post, $variantArray)
    {
        $arrayIdDelete = [];
        if (!empty($post['id_update_old'])) {
            $idUpdateOldArray = explode(System::SEPARATE, $post['id_update_old']);
            $idUpdateArray = [];
            foreach ($variantArray as $row) {
                $variant = json_decode($row, true);
                if ($variant['id_update'] != 0) {
                    $idUpdateArray[] = (int) $variant['id_update'];
                }
            }
            $arrayIdDelete = array_diff($idUpdateOldArray, $idUpdateArray);
            $arrayIdDelete = array_values($arrayIdDelete);
        }
        return $arrayIdDelete;
    }

    /**
     * Save Variant
     */
    public static function saveVariant($post, $parent)
    {
        $variantArray = $post['variant'];
        foreach ($variantArray as $row) {
            //save product child
            $variant = json_decode($row, true);
            self::saveVariantData($parent, $post, $variant);
        }
        $oldIdToDelete = self::getOldIdToDelete($post, $variantArray);
        if (!empty($oldIdToDelete)) {
            self::deleteVariant($oldIdToDelete);
        }
    }

    /**
     * Convert property name
     */
    public static function convertPropertyName($propertyString, $propertyNameArray)
    {
        $propertyStringArray = explode(System::SEPARATE, $propertyString);
        $name = '';
        foreach ($propertyStringArray as $row) {
            if (array_key_exists($row, $propertyNameArray)) {
                $name .= $propertyNameArray[$row].System::SEPARATE.' ';
            }
        }
        $name = substr($name, 0, -2);
        return $name;
    }


    /**
     * Get property and parent name
     */
    public static function getPropertyAndParentName($propertyArray)
    {
        $data = AttributeProperty::select('id', 'name', 'attribute_id')
            ->with(['attribute'])->whereIn('id', $propertyArray)->get();
        $rs = [];
        foreach ($data as $row) {
            $rs[$row->id] = $row->attribute->name.': '.$row->name;
        }
        return $rs;
    }

    /**
     * Get variant to edit
     */
    public static function getVariantToEdit($productId)
    {
        $data = ProductVariant::where('product_id', $productId)->get();
        $idUpdateOldString = [];
        $idUpdateOld = [];
        $property = [];
        $dataConvert = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $idUpdateOld[] = $row->id;
                $propertyStringArray = explode(System::SEPARATE, $row->property_string);
                foreach ($propertyStringArray as $p) {
                    $property[] = $p;
                }
            }
            $idUpdateOldString = implode(System::SEPARATE, $idUpdateOld);
            $propertyNameArray = self::getPropertyAndParentName(array_unique($property));
            foreach ($data as $row) {
                $row->property_string_name = self::convertPropertyName($row->property_string, $propertyNameArray);
                $dataConvert[] = $row;
            }
        }
        $rs = [
            'data' => $dataConvert,
            'idUpdateOld' => $idUpdateOldString
        ];
        return $rs;
    }

    /**
     * END VARIANT
     */


    /**
     * EDIT PRODUCT OPTION
     */

    /**
     * get value by option id
     */
    public static function getValueByOptionArrayId($optionIdArrayKey, $valueTable)
    {
        $valueData = OptionValue::with(['option'])->whereIn('option_id', $optionIdArrayKey)->get();
        $valueDataConvert = [];
        foreach ($valueData as $row) {
            $valueDataConvert[$row->option->id][$row->id] = $row->name;
        }
        return $valueDataConvert;
    }

    /**
     * Convert option
     */
    public static function convertProductToValue($productToValue, $valueTable)
    {
        $optionIdArray = [];
        $valueArray = [];
        foreach ($productToValue as $row) {
            $optionIdArray[$row->option_id] = $row->option_name;
            $valueArray[$row->option_id][] = $row;
        }
        $optionIdArray = array_unique($optionIdArray);
        $optionIdArrayKey = array_keys($optionIdArray);
        $valueDataConvert = self::getValueByOptionArrayId($optionIdArrayKey, $valueTable);
        $data = [
            'option' => $optionIdArray,
            'value' => $valueArray,
            'valueSelect' => $valueDataConvert
        ];
        return $data;
    }

    /**
     * Get option to edit
     */
    public static function getOptionChosen($productId)
    {
        $optionTable = with(new Option())->getTable();
        $valueTable = with(new OptionValue())->getTable();
        $productToOptionTable = with(new ProductToOption())->getTable();
        $productToValue = DB::table($optionTable.' AS o')
            ->select('o.name AS option_name', 'v.name AS value_name', 'po.*')
            ->leftJoin($productToOptionTable. ' AS po', 'po.option_id', '=', 'o.id')
            ->leftJoin($valueTable. ' AS v', 'v.id', '=', 'po.value_id')
            ->where('po.product_id', $productId)
            ->orderBy('po.sort_order', 'asc')
            ->get();
        $data = self::convertProductToValue($productToValue, $valueTable);
        return $data;
    }

    /**
     * END EDIT PRODUCT OPTION
     */
    /**
     * Get all attribute for select
     */
    public static function getAllAttributeForSelect()
    {
        $data = Attribute::select('id', 'name', 'is_filter')->get();
        $select = Functions::convertArrayKeyValue($data, 'id', 'name');
        return $select;
    }

    /**
     * EDIT ATTRIBUTE IN PRODUCT EDIT PAGE
     */
    /**
     * Convert attribute to edit
     */
    public static function convertAttributeToEdit($data)
    {
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[$row->id]['attribute'] = [
                    'name' => $row->name,
                    'id' => $row->id,
                    'is_filter' => $row->is_filter

                ];
                if (!empty($row->property)) {
                    foreach ($row->property as $p) {
                        $rs[$row->id]['property'][$p->id] = $p->name;
                    }
                }
            }
        }
        return $rs;
    }

    /**
     * Get all attribute and property
     */
    public static function getAttributeAndPropertyByAttributeIdArray($attributeIdArray)
    {
        $allAttribute = [];
        if (!empty($attributeIdArray)) {
            $allAttribute = Attribute::with(['property'])->whereIn('id', $attributeIdArray)->get();
        }
        return $allAttribute;
    }

    /**
     * Convert property edit
     */
    public static function convertPropertyEdit($property)
    {
        $rs = [];
        if (!empty($property)) {
            foreach ($property as $row) {
                $rs[$row['attribute_id']] = $row;
            }
        }
        return $rs;
    }

    /**
     * Re sort attribute array
     */
    public static function reSortAttributeArray($attributeConvert, $attributeArraySort)
    {
        $rs = [];
        foreach ($attributeArraySort as $attributeId => $sort) {
            if (array_key_exists($attributeId, $attributeConvert)) {
                $rs[$attributeId] = $attributeConvert[$attributeId];
            }
        }
        return $rs;
    }

    /**
     * Get attribute to edit
     * notice key in $data is: propertyChosen, attribute , attributeValueEdit, isFilter
     */
    public static function getAttributeToEdit($id)
    {
        $propertyEdit = ProductToProperty::where('product_id', $id)->get();
        $propertyChosen = '';
        $propertyChosenArray = [];
        $attributeIdArray = [];
        $attributeArraySort = [];
        $data = [];
        $propertyEditArray = $propertyEdit->toArray();
        if (!empty($propertyEditArray)) {
            foreach ($propertyEdit as $p) {
                if ($p->property_id > 0) {
                    $propertyChosenArray[] = $p->property_id;
                }
                $attributeIdArray[] = $p->attribute_id;
                $attributeArraySort[$p->attribute_id] = $p->sort_order;
            }
            $propertyChosen = implode(System::SEPARATE, array_unique($propertyChosenArray));
        }
        $data['propertyChosen'] = $propertyChosen;
        $attributeEdit = self::getAttributeAndPropertyByAttributeIdArray(array_unique($attributeIdArray));
        $attributeConvert = self::convertAttributeToEdit($attributeEdit);
        $data['attribute'] = self::reSortAttributeArray($attributeConvert, $attributeArraySort);
        $data['propertyEdit'] = self::convertPropertyEdit($propertyEdit->toArray());
        $data['isFilter'] = Attribute::IS_FILTER;
        return $data;
    }
    /**
     * END EDIT ATTRIBUTE IN PRODUCT EDIT PAGE
     */

    /**
     * Convert data of product relation when copying product
     */
    public static function copyProductRelationData($idInsert, $data, $model)
    {
        if (!empty($data)) {
            $dataConvert = [];
            foreach ($data as $row) {
                unset($row['id']);
                $row['product_id'] = $idInsert;
                $dataConvert[] = $row;
            }
            if (!empty($dataConvert)) {
                $model::insert($dataConvert);
            }
        }
    }

    /**
     * Copy product
     */
    public static function copyProduct($productId)
    {
        DB::beginTransaction();
        try {
            $data = Product::with([
                'productToCategory', 'productToOption', 'productToProperty', 'productVariant'
            ])->where('id', $productId)->get()->toArray();
            $product = $data[0];
            $product['name'] = 'Copy of '.$product['name'];
            $productSlug = 'copy-of-'.$product['slug'];
            $product['slug'] = $productSlug;
            $product['created_at'] = Carbon::now()->toDateTimeString();
            unset($product['id']);
            unset($product['product_to_category']);
            unset($product['product_to_option']);
            unset($product['product_to_property']);
            unset($product['product_variant']);
            unset($product['updated_at']);
            $idInsert = Product::insertGetId($product);
            //save productToCategory
            $productToCategory = $data[0]['product_to_category'];
            self::copyProductRelationData($idInsert, $productToCategory, new ProductToCategory());
            //save ProductToOption
            $productToOption = $data[0]['product_to_option'];
            self::copyProductRelationData($idInsert, $productToOption, new ProductToOption());
            //save ProductToProperty
            $productToProperty = $data[0]['product_to_property'];
            self::copyProductRelationData($idInsert, $productToProperty, new ProductToProperty());
            //save ProductVariant
            $productVariant = $data[0]['product_variant'];
            self::copyProductRelationData($idInsert, $productVariant, new ProductVariant());
            //save routes
            $route = [
                'slug' => $productSlug,
                'entity_id' => $idInsert,
                'type' => System::ROUTES_TYPE_PRODUCT
            ];
            Routes::insert($route);
            DB::commit();
            Session::flash('msg', trans('Backend.Lang::lang.msg.copy_product_success'));
            return ['rs'=>System::SUCCESS, 'id'=>$idInsert];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }
}
