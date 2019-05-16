<?php
/**
 * One-to-many form
 * Ajax handle data in many-to-many form
 */
namespace Modules\Backend\Core;

use Illuminate\Http\Request;

class OneToManyController extends BackendGroupController
{

    /**
     * Create item
     */
    public function onCreateItem(Request $request)
    {
        $data['item'] = new \stdClass();
        $data['action'] = $request->action;
        $data['id'] = 0;
        $data['type'] = $request->type;//for attribute
        $data['index'] = $request->index;
        return view('Backend.View::group.'.$request->controller.'.modalItem', $data);
    }

    /**
     * Update item
     */
    public function onUpdateItem(Request $request)
    {
        $data['action'] = $request->action;
        $item = json_decode($request->itemData);
        $data['item'] = $item;
        $data['type'] = $item->type;
        $data['id'] = $item->id;
        $data['index'] = $request->index;
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
        $validate = $model::validateItem($data, $action, $id);
        return response()->json($validate);
    }

    /**
     * append item
     */
    public function onAppendItem(Request $request)
    {
        $item = $request->itemData;
        $data['itemJson'] = $item;
        $data['item'] = json_decode($item);
        $data['index'] = $request->index;
        return view('Backend.View::group.'.$request->controller.'.appendItem', $data);
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