<?php

namespace Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LangController extends Controller
{
    public function postFrontendLang(Request $request)
    {
        Session::put('frontend_locale', $request->frontend_locale);
        return redirect()->back();
    }

    public function postBackendLang(Request $request)
    {
        Session::put('backend_locale', $request->backend_locale);
        return redirect()->back();
    }
}