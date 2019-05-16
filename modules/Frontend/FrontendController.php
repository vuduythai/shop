<?php

namespace Modules\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Modules\Frontend\Classes\Frontend;
use Shipu\Themevel\Facades\Theme as STheme;

class FrontendController extends Controller
{
    /**
     * Set theme for frontend
     */
    public function __construct()
    {
        STheme::set(env('THEME_NAME', 'base'));
        $langMsgJs = STheme::lang('lang.msg_js');
        $data['msg_js'] = json_encode($langMsgJs);
        $data = AppModel::returnCacheData('share_config_frontend', function () use ($data) {
            $data['logo'] = Config::getConfigByKeyCache('logo', '');
            $data['favicon'] = Config::getConfigByKeyCache('favicon', '');
            $data['block'] = Frontend::getAllBlock();
            $data['language'] = System::getLanguage();
            return $data;
        });
        return View::share('share', $data);
    }
}