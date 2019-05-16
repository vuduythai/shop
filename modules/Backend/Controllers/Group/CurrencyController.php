<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Core\System;
use Modules\Backend\Facades\CurrencyFacades;

class CurrencyController extends BackendGroupController
{

    /**
     * Convert currency
     */
    public function onConvert(Request $request)
    {
        $close = $request->closeRs;
        $rs = CurrencyFacades::convertCurrency($close);
        if ($rs['rs'] == System::SUCCESS) {
            Session::flash('msg', trans('Backend.Lang::lang.msg.convert_currency_success'));
        }
        return response()->json($rs);
    }
}