<?php

namespace Modules\Backend\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Models\AttributeGroup;
use Modules\Backend\Models\AttributeProperty;
use Modules\Backend\Models\BackendUser;
use Modules\Backend\Models\Block;
use Modules\Backend\Models\Category;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Label;
use Modules\Backend\Models\Language;
use Modules\Backend\Models\MailTemplate;
use Modules\Backend\Models\Option;
use Modules\Backend\Models\OptionValue;
use Modules\Backend\Models\OrderStatus;
use Modules\Backend\Models\Page;
use Modules\Backend\Models\Payment;
use Modules\Backend\Models\Review;
use Modules\Backend\Models\Role;
use Modules\Backend\Models\Theme;
use Modules\Backend\Models\ThemeImage;
use Modules\Backend\Models\Routes;
use Modules\Backend\Models\Shipping;
use Modules\Backend\Models\TaxClass;
use Modules\Backend\Models\TaxRate;
use Modules\Backend\Models\User;
use Modules\Backend\Models\Weight;
use Modules\Backend\Models\Coupon;
use Modules\Backend\Models\Currency;
use Modules\Backend\Models\Geo;
use Modules\Backend\Models\Length;
use Modules\Backend\Models\Order;
use Modules\Backend\Models\Attribute;
use Modules\Backend\Models\AttributeSet;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\Brand;

class AppModel extends Model
{
    /**
     * generate model based on controller (table)
     * Each model with handle a table
     * controller name is corresponding table
     */
    public static function factoryModelBackend()
    {
        return [
            'config' => new Config(),
            'backend_user' => new BackendUser(),
            'role' => new Role(),
            'theme' => new Theme(),
            'theme_image' => new ThemeImage(),
            'category' => new Category(),
            'currency' => new Currency(),
            'geo' => new Geo(),
            'shipping' => new Shipping(),
            'tax_rate' => new TaxRate(),
            'tax_class' => new TaxClass(),
            'weight' => new Weight(),
            'length' => new Length(),
            'product' => new Product(),
            'routes' => new Routes(),
            'coupon' => new Coupon(),
            'order' => new Order(),
            'attribute' => new Attribute(),
            'attribute_property' => new AttributeProperty(),
            'attribute_set' => new AttributeSet(),
            'attribute_group' => new AttributeGroup(),
            'customer' => new User(),
            'brand' => new Brand(),
            'label' => new Label(),
            'payment' => new Payment(),
            'option' => new Option(),
            'option_value' => new OptionValue(),
            'order_status' => new OrderStatus(),
            'review' => new Review(),
            'page' => new Page(),
            'block' => new Block(),
            'language' => new Language(),
            'mail' => new MailTemplate()
        ];
    }

    /**
     * Return Cache data
     */
    public static function returnCacheData($cacheKey, $function)
    {
        $isCacheEnable = env('IS_CACHE', System::NO);
        $minutes = env('CACHE_MINUTES', 60);
        if ($isCacheEnable == System::CACHE_ON) {
            $value = Cache::remember($cacheKey, $minutes, function () use ($function) {
                return $function();
            });
            return $value;
        } else {
            return $function();
        }
    }

    /**
     * Get cache status
     */
    public static function getCacheStatus()
    {
        $data = Config::select('value')->where('slug', 'is_cache_need_deleted')->first();
        $rs = System::CACHE_NOT_NEED_DELETE;
        if (!empty($data)) {
            $rs = $data->value;
        }
        return $rs;
    }

    /**
     * Validate data then save record
     */
    public static function returnValidateResult($data, $rule, $msgValidate, $controller, $close)
    {
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            $modelArray = AppModel::factoryModelBackend();
            if (array_key_exists($controller, $modelArray)) {
                return $modelArray[$controller]::saveRecord($data, $close);
            } else {
                $rs = ['rs'=>System::FAIL, 'msg'=>__('Backend.Lang::lang.validate.not_found_model')];
                return $rs;
            }
        }
    }

    /**
     * Validate data and slug in table #_routes then save record
     */
    public static function validateSlugData($data, $rule, $msgValidate, $controller, $routeType, $close)
    {
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            $validateSlug = Routes::validateRouteSlugUnique($data, $routeType);
            if ($validateSlug['rs'] == System::SUCCESS) {
                $modelArray = AppModel::factoryModelBackend();
                if (array_key_exists($controller, $modelArray)) {
                    return $modelArray[$controller]::saveRecord($data, $close);
                } else {
                    $rs = ['rs'=>System::FAIL, 'msg'=>__('Backend.Lang::lang.validate.not_found_model')];
                    return $rs;
                }
            }
            return $validateSlug;
        }
    }
}