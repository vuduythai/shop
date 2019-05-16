@extends('Backend.View::layout.main')
<?php
$controllerNameConvert = ucfirst(str_replace('_', ' ', $controller));
?>
@section('title')
{{ __('Backend.Lang::lang.manager.config_default') }}
@endsection

@section('content')

@include('Backend.View::share.breadCrumb')

@if (session('msg'))
<div id="msg_display"
     style="display: none">{{session('msg')}}</div>
@endif

<input type="hidden" id="controller-name" value="{{$controller}}" />
    <!-- form filter -->
<div class="row">
    <div class="col-md-12">
        <div class="form-box-shadow">
            <div class="form-box-header">
                <div class="row">
                    <div class="col-md-6">
                        <p>{{ ucfirst($controllerNameConvert) }}</p>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary btn_save_and_close pull-right"
                                attr-controller="{{ $controller }}" id="save-{{$controller}}">
                            {{ __('Backend.Lang::lang.action.update')}}
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-box-content">
                {{Form::open(['url'=>route('config.store'), 'class'=>'form_dynamic'])}}
                    <ul class="nav nav-tabs">
                        <li class="active nav-item">
                            <a href="#general" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.general')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#order" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.order')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#mail" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.mail')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#customer" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.customer')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#filter" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.filter')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#router" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.router')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#store" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.store')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#homepage" class="nav-link" data-toggle="tab">
                                {{__('Backend.Lang::lang.general.homepage')}}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB GENERAL -->
                        <div class="tab-pane active" id="general">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['currency_default'])
                                    @include('Backend.View::layout.form', $form['currency_service'])
                                    @include('Backend.View::layout.form', $form['currency_service_api'])
                                    @include('Backend.View::layout.form', $form['weight_based_default'])
                                    @include('Backend.View::layout.form', $form['default_attribute_set_id'])
                                </div>
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['review_allow_customer_create'])
                                    @include('Backend.View::layout.form', $form['review_approve_automatic'])
                                    @include('Backend.View::layout.form', $form['logo'])
                                    @include('Backend.View::layout.form', $form['favicon'])
                                </div>
                            </div>
                        </div>

                        <!-- TAB ORDER -->
                        <div class="tab-pane" id="order">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['coupon_prefix'])
                                    @include('Backend.View::layout.form', $form['paypal'])
                                    @include('Backend.View::layout.form', $form['paypal_mode'])
                                    @include('Backend.View::layout.form', $form['paypal_id'])
                                </div>
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['coupon_length_random'])
                                    @include('Backend.View::layout.form', $form['stripe'])
                                    @include('Backend.View::layout.form', $form['stripe_publish'])
                                    @include('Backend.View::layout.form', $form['stripe_secret'])
                                </div>
                            </div>
                        </div>

                        <!-- TAB MAIL -->
                        <div class="tab-pane" id="mail">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['host'])
                                    @include('Backend.View::layout.form', $form['port'])
                                    @include('Backend.View::layout.form', $form['encryption'])
                                    @include('Backend.View::layout.form', $form['username'])
                                    @include('Backend.View::layout.form', $form['password'])
                                </div>
                                <div class="col-md-6">

                                    @include('Backend.View::layout.form', $form['from_email'])
                                    @include('Backend.View::layout.form', $form['from_name'])
                                    @include('Backend.View::layout.form', $form['mail_method'])
                                </div>
                            </div>
                        </div>

                        <!-- TAB CUSTOMER -->
                        <div class="tab-pane" id="customer">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['password_default_customer_create'])
                                </div>
                            </div>
                        </div>

                        <!-- TAB FILTER -->
                        <div class="tab-pane" id="filter">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['category_page_size'])
                                    @include('Backend.View::layout.form', $form['display_price_slider'])
                                    @include('Backend.View::layout.form', $form['display_brand'])
                                </div>
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['display_rating'])
                                    @include('Backend.View::layout.form', $form['display_search'])
                                </div>
                            </div>
                        </div>

                        <!-- TAB ROUTER -->
                        <div class="tab-pane" id="router">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['is_router_one_level'])
                                    <div class="form-group">
                                        <span class="form-comment">
                                            {{ __('Backend.Lang::lang.comment.router_choose_off')}}<br/>
                                            {{ __('Backend.Lang::lang.comment.router_product')}}<br/>
                                            {{ __('Backend.Lang::lang.comment.router_category')}}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['two_level_router_product'])
                                    @include('Backend.View::layout.form', $form['two_level_router_category'])
                                    @include('Backend.View::layout.form', $form['two_level_router_page'])
                                </div>
                            </div>
                        </div>

                        <!-- TAB STORE -->
                        <div class="tab-pane" id="store">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['store_phone'])
                                    @include('Backend.View::layout.form', $form['store_email'])
                                    @include('Backend.View::layout.form', $form['store_address'])
                                </div>
                            </div>
                        </div>

                        <!-- HOMEPAGE SEO -->
                        <div class="tab-pane" id="homepage">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['is_featured_product'])
                                    @include('Backend.View::layout.form', $form['is_new'])
                                    @include('Backend.View::layout.form', $form['is_bestseller'])
                                    @include('Backend.View::layout.form', $form['is_on_sale'])
                                    @include('Backend.View::layout.form', $form['seo_title'])
                                    @include('Backend.View::layout.form', $form['seo_keyword'])
                                </div>
                                <div class="col-md-6">
                                    @include('Backend.View::layout.form', $form['is_featured_product_num'])
                                    @include('Backend.View::layout.form', $form['is_new_num'])
                                    @include('Backend.View::layout.form', $form['is_bestseller_num'])
                                    @include('Backend.View::layout.form', $form['is_on_sale_num'])
                                    @include('Backend.View::layout.form', $form['seo_description'])
                                </div>
                            </div>
                        </div>

                    </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
</div>
@stop