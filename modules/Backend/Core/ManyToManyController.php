<?php
/**
 * Many-to-many form
 * Ajax handle data in many-to-many form
 */
namespace Modules\Backend\Core;

use Illuminate\Http\Request;

class ManyToManyController extends BackendGroupController
{

    /**
     * Create item
     */
    public function onCreateItem(Request $request)
    {
        $data['item'] = new \stdClass();
        $data['action'] = $request->action;
        $data['id'] = 0;
        return view('Backend.View::group.'.$request->controller.'.modalItem', $data);
    }

    /**
     * Update item
     */
    public function onUpdateItem(Request $request)
    {
        $id = $request->id;
        $data['action'] = $request->action;
        $key = $request->modelItem;
        $model = Functions::getFactoryModel($key);
        $data['item'] = $model::getItemById($id);
        $data['id'] = $id;
        return view('Backend.View::group.'.$request->controller.'.modalItem', $data);
    }

    /**
     * Store item
     */
    public function onStoreItem(Request $request)
    {
        $formData = $request->formData;
        $action = $request->action;
        $id = $request->id;
        $data = [];
        foreach ($formData as $row) {
            $data[$row['name']] = $row['value'];
        }
        $key = $request->modelItem;
        $model = Functions::getFactoryModel($key);
        $validate = $model::validateItemAndSave($data, $action, $id);
        return response()->json($validate);
    }

    /**
     * append item
     */
    public function onAppendItem(Request $request)
    {
        $data = [];
        $id = $request->id;
        $key = $request->modelItem;
        $model = Functions::getFactoryModel($key);
        $data['item'] = $model::getItemById($id);
        return view('Backend.View::group.'.$request->controller.'.appendItem', $data);
    }

    /**
     * Attach item
     */
    public function onAttachItem(Request $request)
    {
        $key = $request->modelItem;
        $model = Functions::getFactoryModel($key);
        $data['items'] = $model::getItemPage($request->page);
        return view('Backend.View::group.'.$request->controller.'.attachItem', $data);
    }

    /**
     * Delete item
     */
    public function onDeleteItem(Request $request)
    {
        $idArray = $request->idArray;
        $key = $request->modelItem;
        $model = Functions::getFactoryModel($key);
        $rs = $model::deleteItem($idArray);
        return response()->json($rs);
    }

    /**
     * On edit theme
     */
    public function onEditParent(Request $request)
    {
        $id = $request->id;
        $key = $request->modelItem;
        $model = Functions::getFactoryModel($key);
        $data['items'] = $model::getItemByParentId($id);
        return view('Backend.View::group.'.$request->controller.'.updateItem', $data);
    }
}