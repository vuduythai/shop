<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Illuminate\Support\Facades\Session;

class ConfigController extends BackendGroupController
{
    /**
     * index
     */
    public function index(Request $request)
    {
        $controller = 'config';
        $action = 'index';
        $form = Config::formCreate($controller);
        return view('Backend.View::config.index', compact('form', 'controller', 'action'));
    }

    /**
     * Save config
     */
    public function store(Request $request)
    {
        $post = $request->all();
        $data = $post['formData'];
        $close = $post['closeRs'];
        $rs = Config::saveConfig($data, $close);
        if ($rs['rs'] == System::SUCCESS) {
            Session::flash('msg', __('Backend.Lang::lang.msg.update_success'));
        }
        return response()->json($rs);
    }

}