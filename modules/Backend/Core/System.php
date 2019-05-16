<?php
/**
 * Has constant and functions that can use in all system
 */
namespace Modules\Backend\Core;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Language;
use Modules\Backend\Models\RolePermission;
use Modules\Backend\Models\Permission;

class System
{
    const CACHE_ON = 1;
    const CACHE_OFF = 0;
    const CACHE_DEFAULT_TIME = 60;//60 minutes

    const CACHE_NEED_DELETE = 1;
    const CACHE_NOT_NEED_DELETE = 0;

    const ROLE_ADMINISTRATOR = 1;
    const ROLE_EDITOR = 2;
    const ROLE_BLOGGER = 3;

    const ALLOW = 1;
    const DENY = 0;

    const PAGE_SIZE_DEFAULT = 12;

    const POSITION_BEFORE = 0;
    const POSITION_AFTER = 1;

    const RETURN_SUCCESS = 1;
    const RETURN_FAIL = 0;

    const PRODUCT_TYPE_SIMPLE = 1;
    const PRODUCT_TYPE_CONFIGURABLE = 2;

    const ROUTES_TYPE_PRODUCT = 1;
    const ROUTES_TYPE_CATEGORY = 2;
    const ROUTES_TYPE_PAGE = 3;

    const MAIL_SEND = 0;
    const MAIL_QUEUE = 1;

    const SANDBOX = 0;
    const PRODUCTION = 1;

    const FOLDER_IMAGE = '/upload/image/';

    const IN_STOCK = 1;
    const OUT_OF_STOCK = 0;

    const PAYMENT_STATUS_NOT_PAID = 0;
    const PAYMENT_STATUS_PAID = 1;

    const DISABLE = 0;
    const ENABLE = 1;
    const NO = 0;
    const YES = 1;
    const FAIL = 0;
    const SUCCESS = 1;
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;


    const ACTION_CREATE = 0;
    const ACTION_UPDATE =1;

    const WAIT_ME_COLOR = '#317def';

    const NO_SORT = 0;
    const SORT = 1;

    const TYPE_FIX_AMOUNT = 1;
    const TYPE_PERCENTAGE = 2;

    const SEPARATE = ';';

    const COUPON_PREFIX_DEFAULT = 'shop_';
    const COUPON_LENGTH_RANDOM_DEFAULT = '10';

    const PASSWORD_DEFAULT = '123456';

    const ROUTER_ONE_LEVEL = 1;
    const ROUTER_TWO_LEVEL = 0;

    const FLASH_SUCCESS = 'success';
    const FLASH_ERROR = 'error';

    const PAYPAL_SANDBOX = 0;
    const PAYPAL_PRODUCTION = 1;

    //1: product, 2: category, 3: page

    /**
     * Get current controller
     */
    public static function getCurrentController()
    {
        $backendGroupController = new BackendGroupController();
        $uri = $backendGroupController->getCurrentUri();
        $uriArray = explode('/', $uri);
        $controller = $uriArray[0];
        return $controller;
    }

    /**
     * Generate array status value => name
     */
    public static function mapStatus()
    {
        $array = [
            self::STATUS_UNACTIVE => __('Backend.Lang::lang.general.disable'),
            self::STATUS_ACTIVE => __('Backend.Lang::lang.general.enable')
        ];
        return $array;
    }

    /**
     * Generate select drop down status
     */
    public static function statusSelect()
    {
        $data = self::mapStatus();
        $rs = '';
        $rs[''] = __('Backend.Lang::lang.select.select_status');
        foreach ($data as $key => $value) {
            $rs[$key] = $value;
        }
        return $rs;
    }

    /**
     * Convert Status text for obj
     */
    public static function convertStatus($obj)
    {
        foreach ($obj as $row) {
            switch ($row->status) {
                case System::STATUS_ACTIVE:
                    $row->status = __('Backend.Lang::lang.general.enable');
                    break;
                case System::STATUS_UNACTIVE:
                    $row->status = __('Backend.Lang::lang.general.disable');
                    break;
                default:
                    $row->status = __('Backend.Lang::lang.general.disable');
            }
        }
        return $obj;
    }


    /**
     * Mail select array
     */
    public static function mailSelect()
    {
        return [
            self::MAIL_SEND => __('Backend.Lang::lang.config.send'),
            self::MAIL_QUEUE => __('Backend.Lang::lang.config.queue'),
        ];
    }

    /**
     * Is currency covert
     */
    public static function paypalModeSelect()
    {
        return [
            self::SANDBOX => __('Backend.Lang::lang.config.sandbox'),
            self::PRODUCTION => __('Backend.Lang::lang.config.production'),
        ];
    }


    /**
     * system send mail
     * There are four params:
     * email : email of receiver
     * name : name of receiver
     * template: name of mail template
     * subject : subject of mail
     * data: data pass to email
     */
    public static function sendMail($params)
    {
        //set mail config
        $mailConfig = Config::getConfigByKey('mail', '');
        $mailConfigArray = json_decode($mailConfig, true);
        $fromEmail = isset($mailConfigArray['from_email']) ? $mailConfigArray['from_email'] : '';
        $fromName = isset($mailConfigArray['from_name']) ? $mailConfigArray['from_name'] : '';
        $config = [
            'driver'     => 'smtp',
            'host'       => isset($mailConfigArray['host']) ? $mailConfigArray['host'] : '',
            'port'       => isset($mailConfigArray['port']) ? $mailConfigArray['port'] : '',
            'from'       => ['address' => $fromEmail, 'name' => $fromName],
            'encryption' => isset($mailConfigArray['encryption']) ? $mailConfigArray['encryption'] : '',
            'username'   => isset($mailConfigArray['username']) ? $mailConfigArray['username'] : '',
            'password'   => isset($mailConfigArray['password']) ? $mailConfigArray['password'] : ''
        ];
        \Illuminate\Support\Facades\Config::set('mail', $config);
        //set mail config

        $mailMethod = Config::getConfigByKey('mail_method', self::MAIL_SEND);
        if ($mailMethod == self::MAIL_SEND) {//send
            Mail::to($params['email'], $params['name'])->send(new BaseMail($params));
        } else {//queue
            Mail::to($params['email'], $params['name'])->queue(new BaseMail($params));
        }
    }

    /**
     * Get type fixed amount or percentage
     */
    public static function getTypeFixPer()
    {
        $data = [
            self::TYPE_FIX_AMOUNT => __('Backend.Lang::lang.general.fix_amount'),
            self::TYPE_PERCENTAGE => __('Backend.Lang::lang.general.percentage')
        ];
        return $data;
    }

    /**
     * Display type text
     */
    public static function displayTypeText($type)
    {
        $arrayType = self::getTypeFixPer();
        $text = '';
        if (array_key_exists($type, $arrayType)) {
            $text = $arrayType[$type];
        }
        return $text;
    }

    /**
     * Array yes or no
     */
    public static function yesOrNoArray()
    {
        return [
            self::NO => trans('Backend.Lang::lang.general.no'),
            self::YES => trans('Backend.Lang::lang.general.yes')
        ];
    }

    /**
     * Get message js
     */
    public static function getMsgJs()
    {
        $langMsgJs = Lang::get('Backend.Lang::lang.msg_js');
        $langMsgJsJsonEncode = json_encode($langMsgJs);
        return $langMsgJsJsonEncode;
    }

    /**
     * Add permission to form
     */
    public static function addPermissionForForm($form, $data, $id)
    {
        $form['permission'] = AclResource::aclSource();
        $permissionEdit = [];
        if ($id != '') {
            if (!empty($data->permission)) {
                $permissionEdit = json_decode($data->permission, true);
            }
        }
        $form['permissionEdit'] = $permissionEdit;
        $form['allow'] = System::ALLOW;
        return $form;
    }

    /**
     * Convert permission to save
     */
    public static function convertPermission($data)
    {
        $permission = [];
        $resourceArray = AclResource::getResourceName();
        foreach ($resourceArray as $r) {
            $permission[$r] = 0;
            if (array_key_exists($r, $data)) {
                $permission[$r] = 1;
            }
        }
        return $permission;
    }

    /**
     * Get language
     */
    public static function getLanguage()
    {
        $data = Language::select('name', 'code')->get();
        return $data;
    }

    /**
     * Get favicon
     */
    public static function getFavicon()
    {
        $data = Config::getConfigByKey('favicon', '');
        return $data;
    }
}
