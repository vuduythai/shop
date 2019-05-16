<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Facades\CurrencyFacades;

class Config extends AppModel
{
    protected $table = 'config';
    public $timestamps = false;

    public static function getConfig()
    {
        $data = self::select('slug', 'value')
            ->get();
        $rs = new \stdClass();
        foreach ($data as $row) {
            $slug = $row->slug;
            $rs->$slug = $row->value;
        }
        return $rs;
    }


    /**
     * Get config by key
     * just use in admin - backend
     */
    public static function getConfigByKey($key, $default)
    {
        $data = self::select('value')->where('slug', $key)
            ->first();
        $rs = $default;
        if (!empty($data)) {
            $rs = $data->value;
        }
        return $rs;
    }

    /**
     * Get config by key cache
     * just use in frontend
     */
    public static function getConfigByKeyCache($key, $default)
    {
        $cacheKey = 'config_key_'.$key;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($key, $default) {
            return self::getConfigByKey($key, $default);
        });
        return $rs;
    }

    /**
     * Get config by key 'config'
     * just use in frontend
     */
    public static function getConfigByKeyConfig()
    {
        $rs = self::getConfigByKeyCache('config', '');
        return json_decode($rs, true);
    }


    /**
     * Get config by key in key 'config'
     * just use in backend - admin
     */
    public static function getConfigByKeyInKeyConfig($key, $default)
    {
        $config = self::getConfigByKey('config', '');
        if (!empty($config)) {
            $configObj = json_decode($config);
            if (!empty($configObj->$key)) {
                return $configObj->$key;
            }
        }
        return $default;
    }

    /**
     * Get config by key in key 'config'
     * just use in frontend
     */
    public static function getConfigByKeyInKeyConfigCache($key, $default)
    {
        $cacheKey = 'config_key_in_config_'.$key;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($key, $default) {
            return self::getConfigByKeyInKeyConfig($key, $default);
        });
        return $rs;
    }


    /**
     * Generate form config
     * each array in $arrayField has at least 4 element
     */
    public static function generateFormConfig($data, $arrayField)
    {
        $form = [];

        foreach ($arrayField as $row) {
            $class = 'form-control';
            if ($row[0] == 'select') {
                $class = 'form-control select2';
            }
            $form[$row[1]] = [
                'type' => $row[0],
                'name' => 'config['.$row[1].']',
                'label' => __('Backend.Lang::lang.config.'.$row[1]),
                'value' => BaseForm::formDataAssign($data, $row[1], isset($row[5]) ? $row[5] : ''),
                'class' => $class,
                'data' => $row[2],
                'id' => 'config-'.str_replace(' ', '-', $row[1]),
                'chosen' => isset($row[4]) ? $row[4] : '',
                'comment' => isset($row[6]) ? $row[6] : '',
                'action' => isset($row[7]) ? $row[7] : ''
            ];
        }
        return $form;
    }

    /**
     * Generate mail form
     * 0: type, 1: name, 2: data, 3: assign
     */
    public static function generateMailForm($data, $fieldArray)
    {
        $form = [];
        foreach ($fieldArray as $row) {
            $form[$row[1]] = [
                'type' => $row[0],
                'name' => 'mail['.$row[1].']',
                'label' => __('Backend.Lang::lang.config.'.$row[1]),
                'value' => isset($data[$row[1]]) ? $data[$row[1]] : $row[3],
                'class' => 'form-control',
                'data' => $row[2],
                'id' => 'mail-'.str_replace(' ', '-', $row[1])
            ];
        }
        return $form;
    }

    /**
     * Return form to create and edit
     */
    public static function formCreate($controller)
    {
        $config = Config::getConfigByKey('config', '');
        $configObject = json_decode($config);
        $mailConfig = Config::getConfigByKey('mail', '');
        $mailConfigArray = json_decode($mailConfig, true);
        $mail = System::mailSelect();
        $currency = Currency::currencySelect();
        $weight = Weight::weightSelect();
        $paypalMode = System::paypalModeSelect();
        $attributeSet = AttributeSet::getAttributeSetSelect();
        $currencyService = CurrencyFacades::selectCurrencyConvertService();
        $arrayField = [
            ['select', 'currency_default', $currency, System::NO, [], 1],
            ['text', 'coupon_prefix', [], System::NO, [], 'shop'],
            ['number', 'coupon_length_random', [], System::NO, [], 10],
            ['select', 'weight_based_default', $weight, System::NO, [], 1],
            ['select', 'currency_service', $currencyService, System::NO, [], 0],
            ['password', 'currency_service_api', [], System::NO, [], ''],
            ['switch', 'paypal', [], System::NO, [], System::NO],
            ['radio', 'paypal_mode', $paypalMode, System::NO, [], System::SANDBOX],
            ['text', 'paypal_id', [], ''],
            ['switch', 'stripe', [], System::NO, [], System::NO],
            ['text', 'stripe_publish', [], ''],
            ['password', 'stripe_secret', [], ''],
            ['text', 'password_default_customer_create', [], System::NO, [], '123456'],
            ['text', 'category_page_size', [], System::NO, [], 12],
            ['switch', 'display_price_slider', [], System::YES],
            ['switch', 'display_search', [], System::YES],
            ['switch', 'display_rating', [], System::YES],
            ['switch', 'display_brand', [], System::YES],
            ['select', 'default_attribute_set_id', $attributeSet, System::NO, [], 1],
            ['switch', 'is_router_one_level', [], System::YES],
            ['switch', 'review_allow_customer_create', [], System::YES, [], System::YES],
            ['switch', 'review_approve_automatic', [], System::YES, [], System::YES],
            ['text', 'two_level_router_product', [], System::NO, [], 'product',
                trans('Backend.Lang::lang.comment.two_level_router_product')],
            ['text', 'two_level_router_category', [], System::NO, [], 'category',
                trans('Backend.Lang::lang.comment.two_level_router_category')],
            ['text', 'two_level_router_page', [], System::NO, [], 'page',
                trans('Backend.Lang::lang.comment.two_level_router_page')],
            ['number', 'store_phone', [], System::NO, [], ''],
            ['text', 'store_email', [], System::NO, [], ''],
            ['text', 'store_address', [], System::NO, [], ''],
            ['textarea', 'seo_title', [], System::NO, [], ''],
            ['textarea', 'seo_keyword', [], System::NO, [], ''],
            ['textarea', 'seo_description', [], System::NO, [], ''],
            ['switch', 'is_featured_product', [], System::NO, [], System::NO],
            ['switch', 'is_new', [], System::NO, [], System::NO],
            ['switch', 'is_bestseller', [], System::NO, [], System::NO],
            ['switch', 'is_on_sale', [], System::NO, [], System::NO],
            ['number', 'is_featured_product_num', [], System::NO, [], 5],
            ['number', 'is_new_num', [], System::NO, [], 5],
            ['number', 'is_bestseller_num', [], System::NO, [], 5],
            ['number', 'is_on_sale_num', [], System::NO, [], 5],
        ];
        $form = self::generateFormConfig($configObject, $arrayField);
        $mailField = [
            ['text', 'host', [], ''],
            ['text', 'port', [], ''],
            ['text', 'from_email', [], ''],
            ['text', 'from_name', [], ''],
            ['text', 'encryption', [], ''],
            ['text', 'username', [], ''],
            ['password', 'password', [], ''],
            ['radio', 'mail_method', $mail, ''],
        ];
        $form2 = self::generateMailForm($mailConfigArray, $mailField);

        $obj = new \stdClass();
        $obj->logo = Config::getConfigByKey('logo', '');
        $obj->favicon = Config::getConfigByKey('favicon', '');
        $logo = [
            ['image', 'logo', []],
        ];
        $form3 = BaseForm::generateForm($obj, $controller, $logo);
        $favicon = [
            ['image', 'favicon', []],
        ];
        $form4 = BaseForm::generateForm($obj, $controller, $favicon);

        $formMerge = array_merge($form, $form2, $form3, $form4);
        return $formMerge;
    }

    /**
     * Save config
     */
    public static function saveConfig($data, $close)
    {
        unset($data['_token']);
        $arraySwitch = [
            'is_cache', 'convert_currency', 'paypal', 'stripe', 'display_price_slider',
            'display_search', 'display_rating', 'display_brand', 'is_router_one_level',
            'review_allow_customer_create', 'review_approve_automatic', 'is_featured_product',
            'is_new', 'is_bestseller', 'is_on_sale'
        ];
        foreach ($arraySwitch as $row) {
            $data['config'][$row] = Functions::assignValueIfKeyNotExists($data['config'], $row, System::NO);
        }
        //convert mail and config to json
        $data['mail'] = json_encode($data['mail']);
        $data['config'] = json_encode($data['config']);
        try {
            foreach ($data as $key => $value) {
                $route = self::where('slug', $key)->first();
                if (!empty($route)) {
                    self::where('slug', $key)->update(['value' => $value]);
                } else {
                    $data = [
                        'name' => ucfirst(str_replace('_', ' ', $key)),
                        'slug' => $key,
                        'value' => $value
                    ];
                    self::insert($data);
                }
            }
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

    /**
     * Get currency symbol
     */
    public static function getCurrencySymbol()
    {
        $cacheKey = 'currency_symbol';
        $rs = AppModel::returnCacheData($cacheKey, function () {
            $currencyDefault = self::getConfigByKeyInKeyConfig('currency_default', 1);
            $data = Currency::select('symbol', 'symbol_position', 'code')
                ->where('id', $currencyDefault)
                ->first();
            if (!empty($data)) {
                return $data->toArray();
            } else {
                return ['symbol'=>'$', 'symbol_position' => Currency::POSITION_AFTER, 'code'=>'USD'];//return usd
            }
        });
        return $rs;
    }


}
