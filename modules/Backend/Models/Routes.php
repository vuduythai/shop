<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\System;

class Routes extends AppModel
{
    protected $table = 'routes';
    public $timestamps = false;

    const ROUTE_PRODUCT = 1;
    const ROUTE_CATEGORY = 2;

    /**
     * Save routes
     * $idEdit : to know if create or edit
     * $entityId : id of category (product) just saved
     */
    public static function saveRoutes($idEdit, $slug, $entityId, $type)
    {
        $model = new Routes();
        if ($idEdit != 0) {//edit
            $model = self::where('type', $type)
                ->where('entity_id', $idEdit)
                ->first();
            if (empty($model)) {
                $model = new Routes();
            }
        }
        $model->slug = $slug;
        $model->entity_id = $entityId;
        $model->type = $type;
        $model->save();
    }

    /**
     * Validate slug unique
     */
    public static function validateRouteSlugUnique($post, $type)
    {
        $id = $post['id'];
        $slug = $post['slug'];
        if ($id == 0) {//create
            $rs = self::where('slug', $slug)->first();
        } else {//update
            $rs = self::where('slug', $slug)
                ->where('entity_id', '!=', $id)
                ->where('type', $type)
                ->first();
        }
        if (!empty($rs)) {
            return ['rs'=>System::FAIL, 'msg'=>[__('Backend.Lang::lang.msg.slug_exists')]];
        } else {
            return ['rs'=>System::SUCCESS, 'msg'=>''];
        }
    }


}