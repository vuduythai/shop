<?php

namespace Modules\Frontend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Currency;
use Shipu\Themevel\Facades\Theme as STheme;
use Modules\Frontend\Classes\Frontend;
use Modules\Frontend\Facades\CategoryFacades;
use Modules\Frontend\Facades\CheckOutFacades;
use Modules\Frontend\Facades\ProductFacades;
use Modules\Frontend\FrontendController;

class IndexController extends FrontendController
{

    /**
     * Homepage
     */
    public function index()
    {
        $config = Config::getConfigByKeyConfig();
        $data['seo'] = [
            'seo_title' => $config['seo_title'],
            'seo_keyword' => $config['seo_keyword'],
            'seo_description' => $config['seo_description']
        ];
        $data['const']['yes'] = System::YES;
        $data['category'] = Frontend::getCategoryDisplayInHomePage();
        $data['products'] = Frontend::getFeaturedBestsellerNewOnSale($config);
        return view('pages.home', $data);
    }

    /**
     * Category, product detail and page
     */
    public function slug(Request $request)
    {
        $slug = $request->slug;
        $prefix = $request->prefix;
        $routes = Frontend::handleSlug($prefix, $slug);
        if (!empty($routes['type']) && $routes['type'] == System::ROUTES_TYPE_CATEGORY) {//category
            $limitConfig = Config::getConfigByKeyInKeyConfigCache('category_page_size', System::PAGE_SIZE_DEFAULT);
            isset($request->limit) ? $limit = $request->limit : $limit = $limitConfig;
            $data = CategoryFacades::getListProduct($request, $routes['id'], $limit);
            return view('pages.category', $data);
        } elseif (!empty($routes['type']) && $routes['type'] == System::ROUTES_TYPE_PRODUCT) {//product detail
            $data = ProductFacades::loadProduct($routes['id']);
            return view('pages.productDetail', $data);
        } elseif (!empty($routes['type']) && $routes['type'] == System::ROUTES_TYPE_PAGE) {//page
            $data = Frontend::loadPage($routes['id']);
            return view('pages.page', $data);
        } else {
            return redirect('/404');
        }
    }

    /**
     * Page not found
     */
    public function pageNotFound()
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.404');
        return view('pages.404', $data);
    }

    /**
     * page cart
     */
    public function cart()
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.cart');
        return view('pages.cart', $data);
    }

    /**
     * page checkout
     */
    public function checkout()
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.checkout');
        $data['const'] = CheckOutFacades::constantData();
        $currencyDefault = Config::getConfigByKeyInKeyConfigCache('currency_default', 1);
        $currency =  Currency::getCurrencyById($currencyDefault);
        $data['currency'] = $currency;
        $data['currencyCode'] = $currency['code'];
        $data['ship'] = CheckOutFacades::getAllShip();
        $data['payment'] = CheckOutFacades::getAllPayment();
        $config = Config::getConfigByKeyConfig();
        $data['config'] = $config;
        $user = Auth::guard('users')->user();
        $userExtendData = [];
        if (!empty($user)) {
            $userId = $user->id;
            $userExtendData = CheckOutFacades::getUserExtendData($userId);
        }
        $data['userExtends'] = $userExtendData;
        $data['paypalCancel'] = url('/paypal-cancel');
        $data['paypalSuccess'] = url('/paypal-success');
        $paypalMode = $config['paypal_mode'];
        if ($paypalMode == System::PAYPAL_SANDBOX) {
            $paypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $paypalUrl = 'https://www.paypal.com/cgi-bin/webscr';
        }
        $data['paypalUrl'] = $paypalUrl;
        $data['checkoutUrlOk'] = env('CHECKOUT_URL_OK', 'checkout-ok');
        $data['checkoutUrlFail'] = env('CHECKOUT_URL_FAIL', 'checkout-fail');
        return view('pages.checkout', $data);
    }

    /**
     * Paypal success
     */
    public function paypalSuccess(Request $request)
    {
        $data['breadcrumbName'] = STheme::lang('lang.checkout.paypal_success');
        $data['tokenPaypal'] = $request->token;
        return view('pages.paypalSuccess', $data);
    }

    /**
     * Paypal cancel
     */
    public function paypalCancel()
    {
        $data['breadcrumbName'] = STheme::lang('lang.checkout.paypal_cancel');
        return view('pages.paypalCancel');
    }

    /**
     * Checkout ok
     */
    public function checkoutOk()
    {
        $data['breadcrumbName'] = STheme::lang('lang.checkout.checkout_ok');
        return view('pages.checkoutOk', $data);
    }

    /**
     * Checkout fail
     */
    public function checkoutFail()
    {
        $data['breadcrumbName'] = STheme::lang('lang.checkout.checkout_fail');
        return view('pages.checkoutFailed', $data);
    }

    /**
     * page no permission
     * if user access routes not permission => will be redirect to here
     */
    public function noPermission()
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.no_permission');
        return view('pages.noPermission', $data);
    }

    /**
     * Search
     */
    public function search(Request $request)
    {
        $key = $request->key;
        $page = isset($request->page) ? $request->page : 1;
        $data['breadcrumbName'] = STheme::lang('lang.general.search');
        $data['products'] = Frontend::searchProduct($key, $page);
        $data['key'] = $key;
        return view('pages.search', $data);
    }

    /**
     * Contact us
     */
    public function contactUs(Request $request)
    {
        $data['breadcrumbName'] = STheme::lang('lang.general.contact_us');
        $data['config'] = Config::getConfigByKeyConfig();
        return view('pages.contactUs', $data);
    }
}