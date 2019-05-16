<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Facades\CouponSave;

class CouponController extends BackendGroupController
{
    /**
     * Search Category
     */
    public function onSearchCategory(Request $request)
    {
        $post = $request->all();
        $rs = CouponSave::searchCategory($post);
        return response()->json($rs);
    }

    /**
     * Search product
     */
    public function onSearchProduct(Request $request)
    {
        $post = $request->all();
        $rs = CouponSave::searchProduct($post);
        return response()->json($rs);
    }

}