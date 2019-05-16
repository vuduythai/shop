<?php
/**
 * Just load js for openCkEditor when create field type 'image'
 */
namespace Modules\Backend\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{

    /**
     * On load js
     */
    public function onLoadJs(Request $request)
    {
        $data['id'] = $request->id;
        return view('Backend.View::group.image.js', $data);
    }
}