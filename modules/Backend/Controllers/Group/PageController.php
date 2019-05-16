<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Page;
use Modules\Backend\Models\Routes;

class PageController extends BackendGroupController
{
    /**
     * Override Destroy
     */
    public function destroy($strId)
    {
        $arrayId = explode(System::SEPARATE, $strId);
        foreach ($arrayId as $id) {
            $category = Page::find($id);//create instance to fire event deleted
            $category->delete();
            $route = Routes::where('type', System::ROUTES_TYPE_PAGE)
                ->where('entity_id', $id)
                ->first();
            if (!empty($route)) {
                $route->delete();
            }
        }
        Session::flash('msg', __('Backend.Lang::lang.msg.delete_success'));
        return response()->json(['result'=>System::RETURN_SUCCESS,'msg'=>'']);
    }

}