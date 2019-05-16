<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Models\Category;
use Modules\Backend\Core\System;

class CategoryController extends BackendGroupController
{
    public $controller = 'category';

    /**
     * list category to re-order or edit
     */
    public function index(Request $request)
    {
        $controller = $this->controller;
        $data['controller'] = $controller;
        $category = Category::all()->toHierarchy();
        $data['category']  = $category;
        $parentId = isset($request->parent_id) ? $request->parent_id : 0;
        $id = isset($request->id) ? $request->id : 0;
        $data['form'] = Category::formCreate($request, $controller, $parentId, $id);
        $data['parentId'] = $parentId;
        return view('Backend.View::group.category.index', $data);
    }

    /**
     * Reorder category
     */
    public function onReOrderUpdate(Request $request)
    {
        $params = $request->all();
        $idMove = $params['idMove'];
        $rs = Category::updateNode($params, $idMove);
        return response()->json($rs);
    }

    public function onDeleteCategory(Request $request)
    {
        $rs = Category::deleteCategory($request->id);
        if ($rs['rs'] == System::SUCCESS) {
            Session::flash('msg', __('Backend.Lang::lang.msg.delete_success'));
        }
        return response()->json($rs);
    }
}
