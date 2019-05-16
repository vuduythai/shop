<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\ModelObserve;
use Modules\Backend\Facades\ProductFacades;
use Modules\Backend\Models\Attribute;
use Modules\Backend\Models\Option;
use Modules\Backend\Models\Product;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Routes;
use Illuminate\Support\Facades\Session;

class ProductController extends BackendGroupController
{
    /**
     * append option
     */
    public function onAppendOption(Request $request)
    {
        $optionId = $request->option_id;
        $optionValueData = ProductFacades::getValueByOptionId($optionId);
        $data['optionValue'] = $optionValueData['optionValue'];
        $data['optionValueSelect'] = $optionValueData['optionValueSelect'];
        $data['optionTypeSelect'] = Option::getOptionType();
        $data['optionValueTypeSelect'] = System::getTypeFixPer();
        $data['optionId'] = $optionId;
        $data['optionType'] = $request->option_type;
        return view('Backend.View::group.product.option', $data);
    }

    /**
     * Add more option value
     */
    public function onOptionPlus(Request $request)
    {
        $optionValueSelect = json_decode($request->optionValueSelect, true);
        if (!empty($optionValueSelect)) {
            $keysValue = array_keys($optionValueSelect);
            $data['firstKeyValue'] = $keysValue[0];
            $data['optionValueSelect'] = $optionValueSelect;
        } else {
            return System::FAIL;
        }
        $data['firstKeyType'] = System::TYPE_FIX_AMOUNT;
        $data['optionValueTypeSelect'] = System::getTypeFixPer();
        $data['optionId'] = $request->optionId;
        $data['optionType'] = $request->optionType;
        return view('Backend.View::group.product.optionPlus', $data);
    }

    /**
     * Search property to create variant
     */
    public function onSearchProperty(Request $request)
    {
        $post = $request->all();
        $rs = ProductFacades::searchProperty($post);
        return response()->json($rs);
    }

    /**
     * modal Variant to create
     */
    public function onModalVariant(Request $request)
    {
        $property = $request->property;
        $data['property'] = implode(System::SEPARATE, $property);
        return view('Backend.View::group.product.modalVariant', $data);
    }

    /**
     * Append variant to list
     */
    public function onAppendVariant(Request $request)
    {
        $data = $request->all();
        $data['form'] = $data['formData'];
        $strProperty = '';
        foreach ($data['propertyArray'] as $row) {
            $strProperty .= $row['text'].System::SEPARATE.' ';
        }
        $strProperty = substr($strProperty, 0, -2);
        $data['strProperty'] = $strProperty;
        $data['form']['variant_gallery'] = isset($data['form']['variant_gallery']) ?
            implode(System::SEPARATE, $data['form']['variant_gallery']) :
            '';
        $data['form']['id_update'] = 0;
        return view('Backend.View::group.product.appendVariant', $data);
    }

    /**
     * on modal variant to edit
     */
    public function onModalVariantEdit(Request $request)
    {
        $data = $request->all();
        $data['property'] = $data['variant']['property_string'];
        return view('Backend.View::group.product.modalVariant', $data);
    }

    /**
     * ajax display attribute dynamic in tab attribute
     */
    public function onAttribute(Request $request)
    {
        $post = $request->all();
        $attributeSetId = $post['attributeSetId'];
        $data['attribute'] = ProductFacades::getAttributeProperty($attributeSetId);
        $propertyChosen = '';
        $attributeNotFilterArray = [];
        $data['propertyChosen'] = $propertyChosen;
        $data['attributeValueEdit'] = $attributeNotFilterArray;
        $data['isFilter'] = Attribute::IS_FILTER;
        return view('Backend.View::group.product.attributeCreate', $data);
    }

    /**
     * modal add attribute
     */
    public static function onModalAddAttribute(Request $request)
    {
        $attribute = ProductFacades::getAllAttributeForSelect();
        $data['attribute'] = $attribute;
        return view('Backend.View::group.product.modalAddAttribute', $data);
    }

    /**
     * add attribute
     */
    public static function onAddAttribute(Request $request)
    {
        $post = $request->all();
        $property = ProductFacades::getPropertyByAttributeId($post['id']);
        $post['property'] = Functions::convertArrayKeyValue($property, 'id', 'name');
        $post['attributeId'] = $post['id'];
        return view('Backend.View::group.product.addAttribute', $post);
    }

    /**
     * copy product
     */
    public static function onCopyProduct(Request $request)
    {
        $productId = $request->productId;
        $rs = ProductFacades::copyProduct($productId);
        if ($rs['rs'] == System::SUCCESS) {
            ModelObserve::updateCacheStatus();
        }
        return response()->json($rs);
    }


    /**
     * Override destroy
     */
    public function destroy($strId)
    {
        $arrayId = explode(System::SEPARATE, $strId);
        foreach ($arrayId as $id) {
            $modelInstance = Product::find($id);//create instance to fire event deleted
            $modelInstance->delete();
            $route = Routes::where('entity_id', $id)
                ->where('type', System::ROUTES_TYPE_PRODUCT)
                ->first();
            if (!empty($route)) {
                $route->delete();
            }
        }
        Session::flash('msg', __('Backend.Lang::lang.msg.delete_success'));
        return response()->json(['result'=>System::RETURN_SUCCESS,'msg'=>'']);
    }
}