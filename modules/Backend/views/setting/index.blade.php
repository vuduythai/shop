@extends('Backend.View::layout.main')

@section('title')
{{ __('Backend.Lang::lang.general.dashboard') }}
@endsection

@section('content')

@include('Backend.View::share.breadCrumb')

<?php
$adminUrl = config('app.admin_url');
$user = \Illuminate\Support\Facades\Session::get('admin');
$permission = json_decode($user['permission'], true);
$permission = \Modules\Backend\Core\AclResource::convertResourceForMenu($permission);
$menu1 = [
    ['url'=>'config', 'icon'=>'fa-cogs', 'text'=>__('Backend.Lang::lang.manager.config_manager')],
    ['url'=>'theme', 'icon'=>'fa-object-group', 'text'=>__('Backend.Lang::lang.manager.theme_manager')],
    ['url'=>'block', 'icon'=>'fa-th-large', 'text'=>__('Backend.Lang::lang.manager.block_manager')],
    ['url'=>'page', 'icon'=>'fa-file', 'text'=>__('Backend.Lang::lang.manager.page_manager')],
    ['url'=>'label', 'icon'=>'fa-image', 'text'=>__('Backend.Lang::lang.manager.product_label_manager')],
];
$menu1Config = ['menu'=>$menu1, 'user'=>$user, 'permission'=>$permission, 'adminUrl'=>$adminUrl];
$menu2 = [
    ['url'=>'attribute', 'icon'=>'fa-cog', 'text'=>__('Backend.Lang::lang.manager.attribute_manager')],
    ['url'=>'attribute_group', 'icon'=>'fa-cogs', 'text'=>__('Backend.Lang::lang.manager.attribute_group_manager')],
    ['url'=>'attribute_set', 'icon'=>'fa-cogs', 'text'=>__('Backend.Lang::lang.manager.attribute_set_manager')],
    ['url'=>'option', 'icon'=>'fa-plus-square', 'text'=>__('Backend.Lang::lang.manager.option_manager')],
    ['url'=>'brand', 'icon'=>'fa-navicon', 'text'=>__('Backend.Lang::lang.manager.brand_manager')]
];
$menu2Config = ['menu'=>$menu2, 'user'=>$user, 'permission'=>$permission, 'adminUrl'=>$adminUrl];
$menu3 = [
    ['url'=>'weight', 'icon'=>'fa-th-large', 'text'=>__('Backend.Lang::lang.manager.weight_manager')],
    ['url'=>'length', 'icon'=>'fa-bars', 'text'=>__('Backend.Lang::lang.manager.length_manager')],
    ['url'=>'order_status', 'icon'=>'fa-adjust', 'text'=>__('Backend.Lang::lang.manager.order_status_manager')],
    ['url'=>'order/template', 'icon'=>'fa-print', 'text'=>__('Backend.Lang::lang.manager.invoice_template')],
    ['url'=>'payment', 'icon'=>'fa-money',
        'text'=>__('Backend.Lang::lang.manager.payment_manager')]
];
$menu3Config = ['menu'=>$menu3, 'user'=>$user, 'permission'=>$permission, 'adminUrl'=>$adminUrl];
$menu4 = [
    ['url'=>'currency', 'icon'=>'fa-usd', 'text'=>__('Backend.Lang::lang.manager.currency_manager')],
    ['url'=>'geo', 'icon'=>'fa-building', 'text'=>__('Backend.Lang::lang.manager.geo_manager')],
    ['url'=>'tax_class', 'icon'=>'fa-sitemap', 'text'=>__('Backend.Lang::lang.manager.tax_class_manager')],
    ['url'=>'shipping', 'icon'=>'fa-truck', 'text'=>__('Backend.Lang::lang.manager.shipping_manager')],
    ['url'=>'coupon', 'icon'=>'fa-ticket', 'text'=>__('Backend.Lang::lang.manager.coupon_manager')],
];
$menu4Config = ['menu'=>$menu4, 'user'=>$user, 'permission'=>$permission, 'adminUrl'=>$adminUrl];
$menu5 = [
    ['url'=>'backend_user', 'icon'=>'fa-user', 'text'=>__('Backend.Lang::lang.manager.backend_user_manager')],
    ['url'=>'role', 'icon'=>'fa-group', 'text'=>__('Backend.Lang::lang.manager.role_manager')],
    ['url'=>'customer', 'icon'=>'fa-user', 'text'=>__('Backend.Lang::lang.manager.customer_manager')],
    ['url'=>'review', 'icon'=>'fa-comment', 'text'=>__('Backend.Lang::lang.manager.review_manager')],
    ['url'=>'language', 'icon'=>'fa-flag', 'text'=>__('Backend.Lang::lang.manager.language_manager')],
];
$menu5Config = ['menu'=>$menu5, 'user'=>$user, 'permission'=>$permission, 'adminUrl'=>$adminUrl];
$menu6 = [
    ['url'=>'mail', 'icon'=>'fa-envelope', 'text'=>__('Backend.Lang::lang.manager.mail_manager')],
];
$menu6Config = ['menu'=>$menu6, 'user'=>$user, 'permission'=>$permission, 'adminUrl'=>$adminUrl];

?>

<div class="row">
    @include('Backend.View::setting.menu', $menu1Config)
    @include('Backend.View::setting.menu', $menu2Config)
    @include('Backend.View::setting.menu', $menu3Config)
    @include('Backend.View::setting.menu', $menu4Config)
    @include('Backend.View::setting.menu', $menu5Config)
    @include('Backend.View::setting.menu', $menu6Config)
</div>

@stop
