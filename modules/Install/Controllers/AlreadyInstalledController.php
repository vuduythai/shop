<?php

namespace Modules\Install\Controllers;

use App\Http\Controllers\Controller;

class AlreadyInstalledController extends Controller
{
    public function index()
    {
        return view('Install.View::install.alreadyInstalled');
    }
}