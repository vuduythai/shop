<?php

namespace Modules\Frontend\Classes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Block;
use Modules\Backend\Models\Category;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\ContactUs;
use Modules\Backend\Models\Currency;
use Modules\Backend\Models\Label;
use Modules\Backend\Models\Page;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\ProductToCategory;
use Modules\Backend\Models\Routes;
use Modules\Backend\Models\Theme;
use Modules\Backend\Models\ThemeImage;
use Modules\Backend\Models\ThemeToImage;
use Modules\Backend\Models\Weight;
use Shipu\Themevel\Facades\Theme as STheme;
use Illuminate\Support\Facades\Validator;

class Frontend extends Model
{

    /**
     * Convert image when get images by slug
     */
    public static function convertImages($data)
    {
        $rs = [];
        foreach ($data as $row) {
            $rs[] = $row->image;
        }
        return $rs;
    }

    /**
     * Get theme image
     */
    public static function getThemeImagesBySlug($slug)
    {
        $cacheKey = 'theme_'.$slug;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($slug) {
            $tableTheme = with(new Theme())->getTable();
            $tableThemeToImage = with(new ThemeToImage())->getTable();
            $tableThemeImage = with(new ThemeImage())->getTable();
            $data = DB::table($tableTheme.' AS t')
                ->leftJoin($tableThemeToImage.' AS tti', 't.id', '=', 'tti.theme_id')
                ->leftJoin($tableThemeImage.' AS ti', 'ti.id', '=', 'tti.theme_image_id')
                ->where('t.slug', $slug)
                ->orderBy('tti.sort_order', 'asc')
                ->get();
            $rs = self::convertImages($data);
            return $rs;
        });
        return $rs;
    }

    /**
     * Check single product object
     */
    public static function checkSingleProductCanBuy($data)
    {
        $isQtyOutOfStock = System::OUT_OF_STOCK;
        if ($data->qty > $data->qty_order || $data->qty == 0) {
            $isQtyOutOfStock = System::IN_STOCK;
        }
        $data->is_out_of_stock = $isQtyOutOfStock;
        if ($data->product_type == System::PRODUCT_TYPE_CONFIGURABLE ||
            $data->is_has_option == System::ENABLE ||
            $data->is_in_stock == System::OUT_OF_STOCK ||
            $isQtyOutOfStock == System::OUT_OF_STOCK
        ) {
            $data->action_class = 'view-now';
            $data->action_text = STheme::lang('lang.general.view_now');
        } else {
            $data->action_class = 'buy-now';
            $data->action_text = STheme::lang('lang.general.buy_now');
        }
        return $data;
    }

    /**
     * Check single product array can buy
     */
    public static function checkSingleProductCanBuyArray($data)
    {
        $isQtyOutOfStock = System::OUT_OF_STOCK;
        if ($data['qty'] > $data['qty_order'] || $data['qty'] == 0) {
            $isQtyOutOfStock = System::IN_STOCK;
        }
        $data['is_out_of_stock'] = $isQtyOutOfStock;
        if ($data['product_type'] == System::PRODUCT_TYPE_CONFIGURABLE ||
            $data['is_has_option'] == System::ENABLE ||
            $data['is_in_stock'] == System::OUT_OF_STOCK ||
            $isQtyOutOfStock == System::OUT_OF_STOCK
        ) {
            $data['action_class'] = 'view-now';
            $data['action_text'] = STheme::lang('lang.general.view_now');
        } else {
            $data['action_class'] = 'buy-now';
            $data['action_text'] = STheme::lang('lang.general.buy_now');
        }
        return $data;
    }

    /**
     * Check if product can buy now
     */
    public static function checkProductCanBuy($data)
    {
        foreach ($data as $row) {
            self::checkSingleProductCanBuy($row);
        }
        return $data;
    }

    /**
     * Get list product
     */
    public static function getListProductByField($field, $num)
    {
        $cacheKey = 'list_product_'.$field;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($field, $num) {
            $query = Product::where('status', System::ENABLE)
                ->where($field, System::YES);
            $data = $query->take($num)->get();
            $data = self::checkProductCanBuy($data);
            $data = Price::addFinalPriceForProductObject($data);
            $data = Functions::objectToArray($data);
            $rs['product'] = $data;
            $rs['allLabel'] = Label::getAllProductLabel();
            return $rs;
        });
        return $rs;
    }

    /**
     * Handle slug for page category and product detail
     */
    public static function handleSlug($prefix, $slug)
    {
        $type = '';
        $id = '';
        $config = Config::getConfigByKeyConfig();
        $isRouterOneLevel = isset($config['is_router_one_level']) ?
            $config['is_router_one_level'] : System::ROUTER_TWO_LEVEL;
        if ($isRouterOneLevel == System::ROUTER_ONE_LEVEL) {
            $route = Routes::where('slug', $slug)->first();
            if (!empty($route) && $prefix == '') {
                $type = $route->type;
                $id = $route->entity_id;
            }
        } else {
            $routerTwoLevelCategoryPrefix = isset($config['two_level_router_category']) ?
                $config['two_level_router_category'] : 'category';
            $routerTwoLevelProductPrefix = isset($config['two_level_router_product']) ?
                $config['two_level_router_product'] : 'product';
            $routerTwoLevelPagePrefix = isset($config['two_level_router_page']) ?
                $config['two_level_router_page'] : 'page';
            if ($prefix == $routerTwoLevelCategoryPrefix) {
                $type = System::ROUTES_TYPE_CATEGORY;
                $data = Category::select('id')->where('slug', $slug)->first();
            }
            if ($prefix == $routerTwoLevelProductPrefix) {
                $type = System::ROUTES_TYPE_PRODUCT;
                $data = Product::select('id')->where('slug', $slug)->first();
            }
            if ($prefix == $routerTwoLevelPagePrefix) {
                $type = System::ROUTES_TYPE_PAGE;
                $data = Page::select('id')->where('slug', $slug)->first();
            }
            if (!empty($data)) {
                $id = $data->id;
            }
        }
        $rs = [
            'type' => $type,
            'id' => $id
        ];
        return $rs;
    }

    /**
     * Get breadcrumb for category
     */
    public static function getBreadCrumb($id)
    {
        $model = Category::find($id);
        $parents = $model->getAncestorsAndSelf();
        return $parents->toArray();
    }

    /**
     * Display price and currency for option
     */
    public static function displayPriceAndCurrencyForOption($price)
    {
        $currency = Config::getCurrencySymbol();
        if ($currency['symbol_position'] == Currency::POSITION_BEFORE) {//before
            return $currency['symbol'].' '.$price;
        } else {//after
            return $price.' '. $currency['symbol'];
        }
    }

    /**
     * Convert option form in product detail
     */
    public static function convertOptionToForm($data)
    {
        $rs = [];
        foreach ($data as $row) {
            if ($row['value_price'] != '') {
                if ($row['value_type'] == System::TYPE_FIX_AMOUNT) {
                    $price = self::displayPriceAndCurrencyForOption($row['value_price']);
                } else {
                    $price = $row['value_price'].' %';
                }
                $rs[$row['value_id']] = $row['value_name'].' + '.$price ;
            } else {
                $rs[$row['value_id']] = $row['value_name'];
            }
        }
        return $rs;
    }

    /**
     * Value of option add more - fixed amount or percentage
     */
    public static function valueAddMoreText($type, $price, $valueName)
    {
        if ($price != '') {
            if ($type == System::TYPE_FIX_AMOUNT) {
                $price = self::displayPriceAndCurrencyForOption($price);
            } else {
                $price = $price.' %';
            }
            $rs = $valueName.' + '.$price;
        } else {
            $rs = $valueName;
        }
        return $rs;
    }

    /**
     * Get weight class
     */
    public static function getWeightForShip()
    {
        $cacheKey = 'weight_for_ship';
        $rs = AppModel::returnCacheData($cacheKey, function () {
            $data = Weight::select('id', 'value')->get();
            $rs = [];
            foreach ($data as $row) {
                $rs[$row->id] = $row->value;
            }
            return $rs;
        });
        return $rs;
    }

    /**
     * Convert cart
     */
    public static function convertCart($cart)
    {
        unset($cart['_token']);
        $rs = [];
        $totalPrice = 0;
        $qty = 0;
        $weightTotal = 0;
        $weightBaseDefault = Config::getConfigByKeyInKeyConfigCache('weight_based_default', 1);
        $weightValueArray = self::getWeightForShip();
        !empty($weightValue[$weightBaseDefault]) ?
            $weightBaseDefaultValue = $weightValue[$weightBaseDefault] : $weightBaseDefaultValue = 1;
        foreach ($cart as $key => $value) {
            $totalPricePerItem = $value['qty'] * $value['price'];
            $value['total_price_per_item'] = $totalPricePerItem;
            $totalPrice += $totalPricePerItem;
            $rs['cartDetail'][$key] = $value;
            $qty += $value['qty'];
            !empty($weightValueArray[$value['weight_id']]) ?
                $weightValue = $weightValueArray[$value['weight_id']] : $weightValue = 1;
            $weightTotal +=  ($value['weight'] * ($weightBaseDefaultValue / $weightValue)) ;
        }
        $rs['qtyTotal'] = $qty;
        $rs['totalPrice'] = $totalPrice;
        $rs['weightTotal'] = $weightTotal;
        return $rs;
    }

    /**
     * Convert form from js
     */
    public static function convertFormFromJs($data)
    {
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                if (preg_match('/[\[\]\']/', $row['name'])) {
                    //for example name="gallery[]"
                    $name = substr($row['name'], 0, -2);//remove [] from name
                    $rs[$name][] = $row['value'];
                } else {
                    $rs[$row['name']] = $row['value'];
                }
            }
        }
        return $rs;
    }

    /**
     * Validate form
     */
    public static function validateForm($data, $rule, $msgValidate)
    {
        $validator = Validator::make($data, $rule, $msgValidate);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $msg = $messages->all();
            return ['rs'=>System::FAIL, 'msg'=>$msg];
        } else {
            return ['rs'=>System::SUCCESS, 'msg'=>''];
        }
    }

    /**
     * Search data
     */
    public static function searchProduct($key, $page)
    {
        $cacheKey = 'search_product_'.$key.'_page_'.$page;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($key, $page) {
            $data = Product::where('name', 'like', '%'.$key.'%')
                ->where('status', System::STATUS_ACTIVE)
                ->paginate(System::PAGE_SIZE_DEFAULT, ['*'], 'page', $page);
            $data = self::checkProductCanBuy($data);
            $data = Price::addFinalPriceForProductObject($data);
            return $data;
        });
        return $rs;
    }

    /**
     * Send email contact
     */
    public static function sendContactUsMail($data)
    {
        $params = [
            'email' => $data['store_email'],
            'name' => $data['name'],
            'subject' => STheme::lang('lang.general.contact_us'),
            'data' => $data,
            'template' => 'mails.contactUs'
        ];
        System::sendMail($params);
    }

    /**
     * Save contact us
     */
    public static function saveContactUs($data)
    {
        try {
            $contact = new ContactUs();
            $contact->name = $data['name'];
            $contact->email = $data['email'];
            $contact->phone = $data['phone'];
            $contact->message = $data['message'];
            $contact->save();
            self::sendContactUsMail($data);
            Session::flash('notify_flash_msg', STheme::lang('lang.msg.save_contact_success'));
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'redirect_url'=>'/contact-us'];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

    /**
     * Contact us : validate, save and send email
     */
    public static function contactUs($data)
    {
        $msgValidate = [];
        $rule = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            return self::saveContactUs($data);
        }
    }

    /**
     * Load page
     */
    public static function loadPage($id)
    {
        $cacheKey = 'page_'.$id;
        $rs = AppModel::returnCacheData($cacheKey, function () use ($id) {
            $data = Page::where('id', $id)->first();
            $data = Functions::objectToArray($data);
            $breadCrumb = [
                'type' => System::ROUTES_TYPE_PAGE,
                'data' => [['name'=>$data['name'], 'slug'=>$data['slug']]],
                'name' => $data['name']
            ];
            $seo = [
                'seo_title' => $data['seo_title'] ? $data['seo_title'] : '',
                'seo_keyword' => $data['seo_keyword'] ? $data['seo_keyword'] : '',
                'seo_description' => $data['seo_description'] ? $data['seo_description'] : ''
            ];
            $rs = [
                'page' => $data,
                'breadcrumbArray' => $breadCrumb,
                'seo' => $seo
            ];
            return $rs;
        });
        return $rs;
    }

    /**
     * Get block by slug
     */
    public static function getAllBlock()
    {
        $rs = AppModel::returnCacheData('all_block_data', function () {
            $data = Block::all();
            $rs = [];
            if (!empty($data)) {
                foreach ($data as $row) {
                    $rs[$row->slug] = $row->content;
                }
            }
            return $rs;
        });
        return $rs;
    }

    /**
     * Get product by category
     */
    public static function getProductByCategory($categoryId, $num)
    {
        $productTable = with(new Product())->getTable();
        $productToCategoryTable = with(new ProductToCategory())->getTable();
        $data = DB::table($productTable.' AS p')
            ->leftJoin($productToCategoryTable.' AS ptc', 'ptc.product_id', '=', 'p.id')
            ->where('ptc.category_id', $categoryId)
            ->take($num)
            ->get();
        $data = self::checkProductCanBuy($data);
        $data = Price::addFinalPriceForProductObject($data);
        $data = Functions::objectToArray($data);
        return $data;
    }

    /**
     * Get category display in homepage
     */
    public static function getCategoryDisplayInHomePage()
    {
        $rs = AppModel::returnCacheData('category_in_homepage', function () {
            $data = Category::select('id', 'name', 'num_display')
                ->where('is_homepage', System::YES)
                ->get();
            $rs = [];
            if (!empty($data)) {
                foreach ($data as $row) {
                    if ($row->num_display > 0) {
                        $rs[] = [
                            'name'=>$row->name,
                            'data'=>[
                                'product' => self::getProductByCategory($row->id, $row->num_display),
                                'allLabel' => Label::getAllProductLabel()
                            ]
                        ];
                    }
                }
            }
            return $rs;
        });
        return $rs;
    }

    /**
     * Get featured, bestseller, new, on sale product display in homepage
     */
    public static function getFeaturedBestsellerNewOnSale($config)
    {
        $rs = AppModel::returnCacheData('products_in_homepage', function () use ($config) {
            $arrayKey = ['is_featured_product', 'is_bestseller', 'is_new', 'is_on_sale'];
            $arrayToGetData = [];
            foreach ($arrayKey as $row) {
                if (array_key_exists($row, $config)) {
                    if ($config[$row] == System::YES) {
                        if (array_key_exists($row.'_num', $config)) {
                            $arrayToGetData[$row] = $config[$row.'_num'];
                        }
                    }
                }
            }
            $rs = [];
            if (!empty($arrayToGetData)) {
                foreach ($arrayToGetData as $field => $num) {
                    $rs[] = [
                        'name'=>STheme::lang('lang.home.'.$field),
                        'data'=>self::getListProductByField($field, $num)
                    ];
                }
            }
            return $rs;
        });
        return $rs;
    }

}
