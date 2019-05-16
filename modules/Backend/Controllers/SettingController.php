<?php

namespace Modules\Backend\Controllers;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $controller = 'setting';
        return view('Backend.View::setting.index', compact('controller'));
    }
}