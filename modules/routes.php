<?php

//load module assets
Route::get('/modules/backend/assets/{params}', '\Modules\AssetsController@dataBackend')->where('params', '.*');
Route::get('/modules/install/assets/{params}', '\Modules\AssetsController@dataInstall')->where('params', '.*');
Route::get('/frontend-lang/{frontend_locale}', '\Modules\LangController@postFrontendLang')->middleware('web');
Route::get('/backend-lang/{backend_locale}', '\Modules\LangController@postBackendLang')->middleware('web');

Route::post('/group/store', '\Modules\Backend\Core\BackendGroupController@store')
    ->middleware('web');//to keep session flash

$adminUrl = config('app.admin_url');

//call dynamic action ajax in admin
Route::post('/'.$adminUrl.'/{controller}/{ajax}', function ($controller, $ajax) {
    if (strpos($ajax, 'on') !== false) {//check action has to have string 'on'
        $controllerName = ucfirst($controller).'Controller';
        if ($controller == 'many' || $controller == 'one') {
            $controllerName = ucfirst($controller).'ToManyController';
            return App::call("\\Modules\\Backend\\Core\\" . $controllerName."@".$ajax);
        } else {
            return App::call("\\Modules\\Backend\\Controllers\\Group\\" . $controllerName."@".$ajax);
        }
    }
})->middleware(['web', 'backend.locale']);

//call dynamic action ajax in frontend
Route::post('/{ajax}', function ($ajax) {
    if (strpos($ajax, 'on') !== false) {//check action has to have string 'on'
        return App::call("\\Modules\\Frontend\\Controllers\\AjaxController@".$ajax);
    }
})->middleware(['web', 'frontend.locale']);

Route::get('/already-install', '\Modules\Install\Controllers\AlreadyInstalledController@index')
    ->name('install.already_install')->middleware('web');
//route for install app
Route::group(['middleware' => ['web', 'canInstall'], 'prefix'=>'install',
    'namespace' => '\Modules\Install\Controllers'], function () {
        Route::get('/', 'InstallController@requirement');
        Route::get('/configuration', 'InstallController@configuration')->name('install.configuration');
        Route::post('/validate-config', 'InstallController@onValidateConfig');
        Route::get('/complete', 'InstallController@complete');
});


//route for admin
Route::group(['middleware' => ['web', 'backend.locale'], 'prefix'=>$adminUrl,
    'namespace' => '\Modules\Backend\Controllers'], function () {
        Route::get('/login', 'LoginController@login');
        Route::post('/do-login', 'LoginController@doLogin');
        Route::get('/logout', 'LoginController@logout');
        Route::post('/do-login', 'LoginController@doLogin');
        Route::group(['middleware' => ['backend.auth','backend.acl']], function () {
            Route::get('/', 'DashboardController@index')->name('adminDashboard');
            Route::get('/dashboard/clear-cache', 'DashboardController@clearCache');
            Route::get('/dashboard/deny-acl-view', 'DashboardController@denyAclView');
            Route::any('/dashboard/deny-acl', 'DashboardController@denyAcl');//type post and delete when check acl

            Route::resource('/setting', 'SettingController');
            Route::resource('/backend_user', 'Group\BackendUserController');
            Route::resource('/role', 'Group\RoleController');
            Route::resource('/theme', 'Group\ThemeController');
            Route::resource('/geo', 'Group\GeoController');
            Route::resource('/shipping', 'Group\ShippingController');
            Route::resource('/tax_class', 'Group\TaxClassController');
            Route::resource('/attribute_set', 'Group\AttributeSetController');
            Route::resource('/attribute_group', 'Group\AttributeGroupController');
            Route::resource('/brand', 'Group\BrandController');
            Route::resource('/label', 'Group\LabelController');
            Route::resource('/weight', 'Group\WeightController');
            Route::resource('/length', 'Group\LengthController');
            Route::resource('/coupon', 'Group\CouponController');
            Route::resource('/attribute', 'Group\AttributeController');
            Route::resource('/product', 'Group\ProductController');
            Route::resource('/customer', 'Group\CustomerController');
            Route::resource('/payment', 'Group\PaymentController');
            Route::resource('/config', 'Group\ConfigController');
            Route::resource('/option', 'Group\OptionController');
            Route::resource('/order_status', 'Group\OrderStatusController');
            Route::resource('/review', 'Group\ReviewController');
            Route::resource('/page', 'Group\PageController');
            Route::resource('/block', 'Group\BlockController');
            Route::resource('/language', 'Group\LanguageController');
            Route::resource('/mail', 'Group\MailController');

            Route::post('/currency-convert', 'Group\CurrencyController@onConvert');//for acl
            Route::resource('/currency', 'Group\CurrencyController');

            Route::post('/category-re-order-update', 'Group\CategoryController@onReOrderUpdate');//for acl
            Route::post('/category-delete', 'Group\CategoryController@onDeleteCategory');//for acl
            Route::resource('/category', 'Group\CategoryController');

            Route::get('/order/invoice/{id}', 'Group\OrderController@invoice');
            Route::get('/order/template', 'Group\OrderController@template');
            Route::post('/invoice-save-template', 'Group\OrderController@invoiceSaveTemplate')
                ->name('order.invoice_save_template');
            Route::post('/order-change-order-status', 'Group\OrderController@onChangeOrderStatusHistory');
            Route::post('/order-change-payment-status', 'Group\OrderController@onChangePaymentStatus');
            Route::resource('/order', 'Group\OrderController');
        });
});

//route for frontend
Route::group(['middleware' => ['web', 'frontend.locale'], 'namespace' => '\Modules\Frontend\Controllers'], function () {
    Route::get('/', 'IndexController@index');
    Route::get('/cart', 'IndexController@cart');
    Route::get('/checkout', 'IndexController@checkout');
    Route::get('/no-permission', 'IndexController@noPermission');
    $checkoutUrlOk = env('CHECKOUT_URL_OK', 'checkout-ok');
    $checkoutUrlFail = env('CHECKOUT_URL_FAIL', 'checkout-fail');
    Route::get('/'.$checkoutUrlOk, 'IndexController@checkoutOk');
    Route::get('/'.$checkoutUrlFail, 'IndexController@checkoutFail');
    Route::get('/search', 'IndexController@search');
    Route::get('/contact-us', 'IndexController@contactUs');

    Route::get('/register', 'UserController@register');
    Route::get('/active/{code}', 'UserController@active');
    Route::get('/login', 'UserController@login');
    Route::get('/logout', 'UserController@logout');
    Route::get('/forgot-password', 'UserController@forgotPassword');

    //need authentication
    Route::group(['middleware' => ['frontend.auth']], function () {
        Route::get('/user/change-password', 'UserController@changePassword');
        Route::get('/user/address-manager', 'UserController@addressManager');
        Route::get('/user/order-manager', 'UserController@orderManager');
        Route::get('/order/detail/{id}', 'UserController@orderDetail');
        Route::get('/order/invoice/{id}', 'UserController@orderInvoice');
    });

    //paypal
    Route::get('/paypal-success', 'IndexController@paypalSuccess');
    Route::get('/paypal-cancel', 'IndexController@paypalCancel');

    //load category, product detail, page
    Route::get('/404', 'IndexController@pageNotFound');
    Route::get('/{slug}', 'IndexController@slug')->name('slug');
    Route::get('/{prefix}/{slug}', 'IndexController@slug')->name('slug');
});

