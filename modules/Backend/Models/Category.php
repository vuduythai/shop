<?php

namespace Modules\Backend\Models;

use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Baum\Node;
use Modules\Backend\Core\System;

class Category extends Node
{
    protected $table = 'categories';

    const IS_ROOT = 0;
    const IS_SUB = 1;

    /**
     * Get custom nest list, add seperate '-' before name and filter by params
     */
    public static function getCustomNestList($params)
    {
        $instance = new static;
        $query = $instance->newNestedSetQuery();//use newNestedSetQuery()
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $nodes = $query->paginate(System::PAGE_SIZE_DEFAULT);
        foreach ($nodes as $row) {
            $row->name = str_repeat('-', $row->depth) . ' ' . $row->name;
        }
        return $nodes;
    }

    /**
     * Return form to create and edit
     */
    public static function formCreate($request, $controller, $parentId, $id = '')
    {
        $data = new \stdClass();
        if ($id != '') {//edit
            $data = self::find($id);
        }
        $categoryArray = Category::listCategoryForProduct();
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['text', 'slug', [], System::YES],
            ['select', 'parent_id', $categoryArray, System::YES, '', '', ''],
            ['textarea', 'description', []],
            ['switch', 'status', [], System::NO, [], System::ENABLE],
            ['textarea', 'seo_title', []],
            ['textarea', 'seo_keyword', []],
            ['textarea', 'seo_description', []],
            ['image', 'image', []],
            ['switch', 'is_homepage', [], System::NO, [], System::NO],
            ['number', 'num_display', [], System::NO, [], 0],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [];
        $rule = [
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ];
        if ($data['id'] != 0) {
            $rule['slug'] = 'required|unique:categories,slug,'.$data['id'];
        }
        $routeType = System::ROUTES_TYPE_CATEGORY;
        return AppModel::validateSlugData($data, $rule, $msgValidate, $controller, $routeType, $close);
    }

    /**
     * Save Category
     */
    public static function saveCategory($data)
    {
        $id = $data['id'];
        $model = new Category();
        if ($id != 0) {//edit
            $model = self::find($id);
        }
        $model->name = $data['name'];
        $model->slug = $data['slug'];
        if ($data['is_root'] == self::IS_SUB) {
            $model->parent_id = $data['parent_id'];
        }
        $model->status = isset($data['status']) ? $data['status'] : System::NO;
        $model->image = $data['image'];
        $model->description = $data['description'];
        $model->seo_title = $data['seo_title'];
        $model->seo_keyword = $data['seo_keyword'];
        $model->seo_description = $data['seo_description'];
        $model->is_homepage =  isset($data['is_homepage']) ? $data['is_homepage'] : System::NO;
        $model->num_display = $data['num_display'];
        $model->save();
        return $model;
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        DB::beginTransaction();
        try {
            $model = self::saveCategory($data);
            Routes::saveRoutes($data['id'], $data['slug'], $model->id, System::ROUTES_TYPE_CATEGORY);
            DB::commit();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }


    /**
     * Get list Category when create product
     */
    public static function listCategoryForProduct()
    {
        $instance = new static;
        $cat = $instance->getNestedList('name', null, '¦-- ');
        return $cat;
    }

    /**
     * Convert category to json
     */
    public static function categoryToJson($category, $tree = [])
    {
        $node = [];
        foreach ($category as $row) {
            $node['name'] = $row->name;
            $node['id'] = $row->id;
            $parentIdArray[] = $row->id;
            if (!empty($row->children)) {
                $childData = [];
                foreach ($row->children as $child) {
                    $childrenOfChild = [];
                    if (!empty($child->children)) {
                        $childrenOfChild = self::categoryToJson($child->children, $childrenOfChild);
                    }
                    $childData[] = [
                        'name' => $child->name,
                        'id' => $child->id,
                        'children' => $childrenOfChild
                    ];
                }
                $node['children'] = $childData;
            }
            $tree[] = $node;
        }
        return $tree;
    }

    /**
     * Get parentIdArray
     */
    public static function getParentIdArray($category)
    {
        $categoryArray = $category->toArray();
        $parentIdArray = [];
        if (!empty($categoryArray)) {
            foreach ($categoryArray as $row) {
                if (!empty($row['children'])) {
                    $parentIdArray[] = $row['id'];
                }
            }
        }
        return $parentIdArray;
    }

    /**
     * Get first node
     */
    public static function getFirstNode($category)
    {
        $firstNodeId = 0;
        if (!empty($category)) {
            $i = 0;
            foreach ($category as $row) {
                if ($i == 0) {
                    $firstNodeId = $row->id;
                }
                $i++;
            }
        }
        return $firstNodeId;
    }

    /**
     * Update note
     */
    public static function updateNode($params, $idMove)
    {
        $node = Category::find($idMove);
        if ($params['parent_id'] == 0) {
            //make this node be root if 'parent_id' == null
            $node->makeRoot();
        } else {
            //make this node be child of some node if 'parent_id' != null
            $nodeParent = Category::find($params['parent_id']);
            $node->makeChildOf($nodeParent);
        }
        if ($params['siblingPrevId'] != 0 && $params['siblingPrevId'] != $idMove) {
            //avoid move to itself
            $nodePrev = Category::find($params['siblingPrevId']);
            $node->moveToRightOf($nodePrev);
        } else {
            if ($params['siblingNextId'] != 0 && $idMove != $params['siblingNextId']) {//avoid move to itself
                $nodeNext = Category::find($params['siblingNextId']);
                $node->moveToLeftOf($nodeNext);
            }
        }
        if (!empty($node)) {
            $rs = ['rs'=>System::RETURN_SUCCESS, 'msg'=>''];
        } else {
            $rs = ['rs'=>System::RETURN_SUCCESS, 'msg'=>''];
        }
        return $rs;
    }


    /**
     * Delete category
     */
    public static function deleteCategory($id)
    {
        try {
            DB::beginTransaction();
            $category = Category::find($id);//create instance to fire event deleted
            $category->delete();
            $route = Routes::where('type', System::ROUTES_TYPE_CATEGORY)
                ->where('entity_id', $id)
                ->first();
            if (!empty($route)) {
                $route->delete();
            }
            DB::commit();
            return ['rs'=>System::RETURN_SUCCESS,'msg'=>''];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL,'msg'=>[$e->getMessage()]];
        }
    }

}