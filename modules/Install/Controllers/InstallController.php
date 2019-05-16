<?php

namespace Modules\Install\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Modules\Install\Facades\Configuration;
use Modules\Install\Helpers\RequirementsCheck;
use Illuminate\Support\Facades\View;

class InstallController extends Controller
{

    public function __construct()
    {
        $langMsgJs = Lang::get('Install.Lang::lang.msg_js');
        $data['msg_js'] = json_encode($langMsgJs);
        return View::share('share', $data);
    }

    /**
     * step 1: requirement
     */
    public function requirement()
    {
        $requirement = new RequirementsCheck();
        $data['checkPhpVersion'] = $requirement->checkPhpVersion();
        $data['extensionCheck'] = $requirement->checkExtension();
        $data['permissionCheck'] = $requirement->checkPermission();
        return view('Install.View::install.requirement', $data);
    }

    /**
     * step 2: configuration
     */
    public function configuration()
    {
        $requirement = new RequirementsCheck();
        $data['checkPhpVersion'] = $requirement->checkPhpVersion();
        $data['extensionCheck'] = $requirement->checkExtension();
        $data['permissionCheck'] = $requirement->checkPermission();
        return view('Install.View::install.configuration', $data);
    }

    /**
     * ajax validate config
     */
    public function onValidateConfig(Request $request)
    {
        $post = $request->all();
        $rs = Configuration::validateAndDoInstall($post['formData']);
        return response()->json($rs);
    }

    /**
     * Step3: complete
     * create installed file to know app is installed
     */
    public function complete()
    {
        $adminUrl = env('ADMIN_URL');
        $data['adminUrl'] = URL::to('/'.$adminUrl);
        $data['baseUrl'] = URL::to('/');
        //create file installed for middleware canInstall
        Configuration::createInstalledFile();
        return view('Install.View::install.complete', $data);
    }


}