<?php
/**
 * Main controller of backend that has CRUD action
 */
namespace Modules\Backend\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Event;
use Modules\Backend\Events\AfterDeleteRecord;
use Modules\Backend\Models\Config;

class BackendGroupController extends Controller
{

    /**
     * Share in all view in action of controller extends BackendGroupController
     */
    public function __construct()
    {
        $data['msg_js'] = System::getMsgJs();
        $data['config'] = Config::getConfigByKey('config', '');
        return View::share('share', $data);
    }

    /**
     * Get current controller based on current url
     */
    public function getCurrentUri()
    {
        $currentURL = URL::current();
        $baseUrl = URL::to('/');
        $adminString = config('app.admin_url');
        $controller = str_replace($baseUrl.'/'.$adminString.'/', '', $currentURL);
        return $controller;
    }

    /**
     * Get list data based on controller
     * Get controller based on current url
     * Then get model by controller name
     * From controller name, call function getList() in each corresponding model
     */
    public function index(Request $request)
    {
        $uri = $this->getCurrentUri();
        $uriArray = explode('/', $uri);
        $controller = $uriArray[0];
        $modelArray = AppModel::factoryModelBackend();
        $data = [];
        $params = $request->all();
        if (array_key_exists($controller, $modelArray)) {
            $model = $modelArray[$controller];
            $rs = $model::getList($params);
            $data = $rs['data'];//list record
            $data->appends($request->all());//append to pagination
        }
        $button = [];
        return view(
            'Backend.View::group.baseList',
            compact('rs', 'data', 'controller', 'params', 'button')
        );
    }

    /**
     * Create
     */
    public function create(Request $request)
    {
        $uri = $this->getCurrentUri();
        $uriArray = explode('/', $uri);
        $controller = $uriArray[0];
        $modelArray = AppModel::factoryModelBackend();
        $action = 'create';
        if (array_key_exists($controller, $modelArray)) {
            $model = $modelArray[$controller];
            $form = $model::formCreate($request, $controller);
        }
        return view('Backend.View::group.baseForm', compact('form', 'controller', 'action'));
    }

    /**
     * Edit
     */
    public function edit(Request $request, $id)
    {
        $uri = $this->getCurrentUri();
        $uriArray = explode('/', $uri);
        $controller = $uriArray[0];
        $modelArray = AppModel::factoryModelBackend();
        $action = 'edit';
        if (array_key_exists($controller, $modelArray)) {
            $model = $modelArray[$controller];
            $form = $model::formCreate($request, $controller, $id);
        }
        return view('Backend.View::group.baseForm', compact('form', 'controller', 'action'));
    }

    /**
     * Destroy
     */
    public function destroy($strId)
    {
        $arrayId = explode(System::SEPARATE, $strId);
        $uri = $this->getCurrentUri();
        $uriArray = explode('/', $uri);
        $controller = $uriArray[0];
        $modelArray = AppModel::factoryModelBackend();
        if (array_key_exists($controller, $modelArray)) {
            $model = $modelArray[$controller];
            foreach ($arrayId as $id) {
                $modelInstance = $model::find($id);//create instance to fire event deleted
                $modelInstance->delete();
            }
        }
        $arrayEvent = ['controller'=>$controller, 'arrayId'=>$arrayId];
        Event::fire(new AfterDeleteRecord($arrayEvent));
        Session::flash('msg', __('Backend.Lang::lang.msg.delete_success'));
        return response()->json(['rs'=>System::RETURN_SUCCESS,'msg'=>'']);
    }

    /**
     * Validate Data then save
     */
    public function store(Request $request)
    {
        $params = $request->all();
        $controller = $params['controller'];
        $formData = $params['formData'];
        $close = $params['closeRs'];
        $modelArray = AppModel::factoryModelBackend();
        if (array_key_exists($controller, $modelArray)) {
            $model = $modelArray[$controller];
            $rs = $model::validateDataThenSave($formData, $controller, $close);
            if ($rs['rs'] == System::SUCCESS) {
                if ($formData['id'] != 0) {//update
                    $msg = __('Backend.Lang::lang.msg.update_success');
                } else {//create
                    $msg = __('Backend.Lang::lang.msg.create_success');
                }
                Session::flash('msg', $msg);
            }
            return response()->json($rs);
        }
        $rs = ['rs'=>System::FAIL, 'msg'=>__('Backend.Lang::lang.validate.not_found_model')];
        return response()->json($rs);
    }

}