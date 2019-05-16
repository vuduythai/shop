<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\Validator;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Facades\Configurable;
use Modules\Backend\Facades\ProductFacades;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Core\AppModel;

class Product extends AppModel
{
    protected $table = 'product';

    /**
     * Has many category
     */
    public function productToCategory()
    {
        return $this->hasMany('Modules\Backend\Models\ProductToCategory', 'product_id', 'id');
    }

    /**
     * Has many option
     */
    public function productToOption()
    {
        return $this->hasMany('Modules\Backend\Models\ProductToOption', 'product_id', 'id');
    }

    /**
     * Has many property
     */
    public function productToProperty()
    {
        return $this->hasMany('Modules\Backend\Models\ProductToProperty', 'product_id', 'id');
    }

    /**
     * Has many variant
     */
    public function productVariant()
    {
        return $this->hasMany('Modules\Backend\Models\ProductVariant', 'product_id', 'id');
    }

    /**
     * key 'relation' is a string: 'relation,field_name'
     */
    public static function getList($params)
    {
        $productType = [
            System::PRODUCT_TYPE_SIMPLE,
            System::PRODUCT_TYPE_CONFIGURABLE
        ];
        $query = self::orderBy('id', 'desc')
            ->whereIn('product_type', $productType);
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        if (isset($params['category'])) {
            $query->whereHas(
                'productToCategory',
                function ($query) use ($params) {
                    $query->where('category_id', $params['category']);
                }
            );
        }
        if (isset($params['product_type'])) {
            $query->where('product_type', $params['product_type']);
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $data = self::convertProductTypeText($data);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name')],
            ['column'=>'price', 'name'=>__('Backend.Lang::lang.field.price')],
            ['column'=>'product_type', 'name'=>__('Backend.Lang::lang.field.type')],
            ['column'=>'status', 'name'=>__('Backend.Lang::lang.field.status'),
                'partial'=>'Backend.View::share.status'],
            ['column'=>'image', 'name'=>__('Backend.Lang::lang.field.image'),
                'partial'=>'Backend.View::share.image'],
        ];
        $productTypeSelect = self::selectProductType();
        $category[''] = __('Backend.Lang::lang.general.select_category');
        $catData = Category::listCategoryForProduct();
        foreach ($catData as $key => $value) {
            $category[$key] = $value;
        }
        $rs = [
            'data' => $data,
            'field' => $field,
            'filter' => [
                'type' => $productTypeSelect,
                'category' => $category
            ],
            'button' => ['Backend.View::group.product.buttonCopy'],
            'filter_template' => 'Backend.View::group.product.filter'
        ];
        return $rs;
    }

    /**
     * Return form to create and edit
     */
    public static function formCreate($request, $controller, $id = '')
    {
        $data = new \stdClass();
        $categoryChosen = '';
        $labelChosen = '';
        $variantJson = '';
        $optionChosen = [];
        $variantEdit = [
            'data' => [],
            'idUpdateOld' => ''
        ];
        $routes = new \stdClass();
        $attributeEdit = [];
        $categoryArray = Category::listCategoryForProduct();
        if ($id != '') {//edit
            $data = Product::find($id);
        }
        $taxClass = TaxClass::taxClassSelect();
        $weightClass = Weight::weightSelect();
        $lengthClass = Length::lengthSelect();
        $attributeSetIdArray = AttributeSet::getAttributeSetSelect();
        $attributeSetDefault = Config::getConfigByKeyInKeyConfig('default_attribute_set_id', 1);
        $brandSelect = Brand::brandSelect();
        $productLabel = Label::labelSelect();
        $yesOrNo = System::yesOrNoArray();
        $arrayField = [
            ['select', 'attribute_set_id', $attributeSetIdArray, System::NO, [], $attributeSetDefault],
            ['text', 'name', [], System::YES],
            ['select', 'category[]', $categoryArray, System::YES, '', '', '', 'multiple="multiple"'],
            ['text', 'slug', [], System::YES],
            ['select', 'tax_class_id', $taxClass, System::YES],
            ['image', 'image', []],
            ['number', 'price', [], System::YES],
            ['number', 'price_promotion', []],
            ['text', 'price_promo_from', []],
            ['text', 'price_promo_to', []],
            ['text', 'sku', []],
            ['switch', 'is_in_stock', [], System::NO, [], System::ENABLE],
            ['text', 'qty', [], System::NO, [], 0],
            ['text', 'weight', [], System::ENABLE, [], 0],//decimal
            ['select', 'weight_id', $weightClass, System::YES],
            ['text', 'sort_order', [], System::NO, [], 0],
            ['select', 'category_default', $categoryArray, System::YES, [], ''],
            ['select', 'brand_id', $brandSelect, System::NO, []],
            ['select', 'product_label[]', $productLabel, System::NO, '', '', '', ' multiple="multiple"'],
            ['switch', 'is_featured_product', [], System::NO, [], System::DISABLE],
            ['switch', 'is_new', [], System::NO, [], System::DISABLE],
            ['switch', 'is_bestseller', [], System::NO, [], System::DISABLE],
            ['switch', 'is_on_sale', [], System::NO, [], System::DISABLE],
            ['radio', 'is_variant_change_image', $yesOrNo, System::NO, [], '',
                __('Backend.Lang::lang.comment.is_variant_change_image')],
            ['switch', 'status', [], System::NO, [], System::ENABLE],

            //field extend
            ['textarea', 'short_intro', []],
            ['textarea', 'full_intro', []],
            ['text', 'tag', [], System::NO, [], '', __('Backend.Lang::lang.comment.tag')],
            ['textarea', 'seo_title', []],
            ['textarea', 'seo_keyword', []],
            ['textarea', 'seo_description', []],
            ['select', 'length_id', $lengthClass, System::YES],
            ['text', 'length', [], System::DISABLE, [], 0],//decimal
            ['text', 'width', [], System::DISABLE, [], 0],
            ['text', 'height', [], System::DISABLE, [], 0],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        $form['gallery'] = !empty($data->gallery) ? $data->gallery : '';
        $form['variant_json'] = $variantJson;
        $form['routes'] = $routes;
        $form['folderImage'] = System::FOLDER_IMAGE;
        if ($id != '') {//edit
            $form['productData'] = $data;
            $category = ProductToCategory::where('product_id', $id)->get();
            $category = Functions::convertObjectValue($category, 'category_id');
            $categoryChosen = implode(System::SEPARATE, $category);
            $labelChosen = $data->product_label;
            $optionChosen = ProductFacades::getOptionChosen($data->id);
            $variantEdit = ProductFacades::getVariantToEdit($id);
            $attributeEdit = ProductFacades::getAttributeToEdit($id);
        }
        $option = Option::getOptionSelect();
        $form['option'] = $option['select'];
        $form['optionType'] = json_encode($option['type']);
        $form['categoryChosen'] = $categoryChosen;
        $form['labelChosen'] = $labelChosen;
        $form['optionChosen'] = $optionChosen;
        $form['optionTypeSelect'] = Option::getOptionType();
        $form['optionValueTypeSelect'] = System::getTypeFixPer();
        $form['variantEdit'] = $variantEdit['data'];
        $form['variantIdUpdateOld'] = $variantEdit['idUpdateOld'];
        $form['attributeEdit'] = $attributeEdit;
        $form['template'] = 'Backend.View::group.product.form';
        return $form;
    }

    /**
     * Validate product
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [];
        $rule = [
            'name' => 'required',
            'slug' => 'required|unique:product',
            'price' => 'required',
            'category' => 'required',
            'price_promo_from' => 'nullable|date',
            'price_promo_to' => 'nullable|date|after_or_equal:price_promo_from'
        ];
        if ($data['id'] != 0) {
            $rule['slug'] = 'required|unique:product,slug,'.$data['id'];
        }
        $routeType = System::ROUTES_TYPE_PRODUCT;
        return AppModel::validateSlugData($data, $rule, $msgValidate, $controller, $routeType, $close);
    }

    /**
     * Save product
     */
    public static function saveRecord($data, $close)
    {
        $rs = ProductFacades::saveAllDataOfProduct($data, $close);
        return $rs;
    }


    /**
     * Select product type
     */
    public static function selectProductType()
    {
        $rs[''] = __('Backend.Lang::lang.general.select_product_type');
        $productTypeArray = self::productTypeArray();
        foreach ($productTypeArray as $key => $value) {
            $rs[$key] = $value;
        }
        return $rs;
    }

    /**
     * Convert product type text
     */
    public static function convertProductTypeText($data)
    {
        $productTypeArray = self::productTypeArray();
        if (!empty($data)) {
            foreach ($data as $row) {
                if (array_key_exists($row->product_type, $productTypeArray)) {
                    $row->product_type = $productTypeArray[$row->product_type];
                }
            }
        }
        return $data;
    }

    /**
     * Product type array for select
     */
    public static function productTypeArray()
    {
        return [
            System::PRODUCT_TYPE_SIMPLE => __('Backend.Lang::lang.general.simple'),
            System::PRODUCT_TYPE_CONFIGURABLE => __('Backend.Lang::lang.general.configurable')
        ];
    }

}