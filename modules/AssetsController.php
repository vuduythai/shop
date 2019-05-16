<?php

namespace Modules;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AssetsController extends Controller
{
    /**
     * multiple public folder
     * https://stackoverflow.com/questions/36336103/laravel-5-multiple-public-folder-asset-function
     */

    public static function getResponseData($fileName, $path)
    {
        $fileArray = explode('/', $fileName);
        $previousIndexBeforeLast = count($fileArray) - 2;
        $type = $fileArray[$previousIndexBeforeLast];//get type
        $path =  $path.$fileName;
        $filePath = File::get($path);
        if ($type == 'css') {
            $contentType = 'text/css';
        } elseif ($type == 'js') {
            $contentType = 'application/javascript';
        } else {
            $contentType = '';
        }
        return ['filePath'=>$filePath, 'contentType'=>$contentType];
    }

    public function dataBackend($fileName)
    {
        $path = base_path().'/modules/Backend/assets/';
        $rs = self::getResponseData($fileName, $path);
        $response = Response::make($rs['filePath'], 200);
        $response->header("Content-Type", $rs['contentType']);
        return $response;
    }

    public function dataInstall($fileName)
    {
        $path = base_path().'/modules/Install/assets/';
        $rs = self::getResponseData($fileName, $path);
        $response = Response::make($rs['filePath'], 200);
        $response->header("Content-Type", $rs['contentType']);
        return $response;
    }

}