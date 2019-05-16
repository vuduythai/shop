@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.home') }}
@stop

@section('content')
@inject('home', 'Modules\Frontend\Components\Home')

@php
$gallery = $home::gallery();
$brand = $home::brand();
@endphp

<div class="container">
    <div class="row">
        <div class="col-md-3" id="service-div">
            <!-- delivery, support -->
            <div class="two-service-item">
                <div class="service-item">
                    <div class="service-icon pull-left">
                        <i class="fa fa-truck" aria-hidden="true"></i>
                    </div>

                    <div class="service-details">
                        <p>{{ Theme::lang('lang.home.free_delivery') }}</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon pull-left">
                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                    </div>

                    <div class="service-details">
                        <p>{{ Theme::lang('lang.home.money_back') }}</p>
                    </div>
                </div>
            </div>

            <div class="two-service-item">
                <div class="service-item">
                    <div class="service-icon pull-left">
                        <i class="fa fa-commenting-o" aria-hidden="true"></i>
                    </div>

                    <div class="service-details">
                        <p>{{ Theme::lang('lang.home.online_support') }}</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon pull-left">
                        <i class="fa fa-gift" aria-hidden="true"></i>
                    </div>

                    <div class="service-details">
                        <p>{{ Theme::lang('lang.home.win_100') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9" id="slider-div">
            <!-- slider -->
            <div id="header-slider">

                <div class="item-slick">
                    <img src="{{ IHelpers::imageDisplaySlir('w861-h316', $gallery[0]) }}" class="slick-image img-responsive"/>
                    <div class="item-slick-animate">
                        <p class="p-capital animate-text-1" data-animation="bounce">Gift When</p>
                        <p class="p-uppercase animate-text-2" data-animation="jackInTheBox delay-1s">Buy Products</p>
                        <a class="p-uppercase button-animate" data-animation="jackInTheBox delay-2s"> Shop Now</a>
                    </div>
                </div>

                <div class="item-slick">
                    <img src="{{ IHelpers::imageDisplaySlir('w861-h316', $gallery[1]) }}" class="slick-image img-responsive"/>
                    <div class="item-slick-animate">
                        <p class="p-capital animate-text-1" data-animation="bounce">Hot deal</p>
                        <p class="p-uppercase animate-text-2" data-animation="jackInTheBox delay-1s">New arrivals</p>
                        <a class="p-uppercase button-animate" data-animation="jackInTheBox delay-2s"> Shop Now</a>
                    </div>
                </div>

                <div class="item-slick">
                    <img src="{{ IHelpers::imageDisplaySlir('w861-h316', $gallery[2]) }}" class="slick-image img-responsive"/>
                    <div class="item-slick-animate">
                        <p class="p-capital animate-text-1" data-animation="bounce">Money back guarantee</p>
                        <p class="p-uppercase animate-text-2" data-animation="jackInTheBox delay-1s">If you're not satisfied</p>
                    </div>
                </div>

                <div class="item-slick">
                    <img src="{{ IHelpers::imageDisplaySlir('w861-h316', $gallery[3]) }}" class="slick-image img-responsive"/>
                    <div class="item-slick-animate">
                        <p class="p-capital animate-text-1" data-animation="bounce">Sale Off</p>
                        <p class="p-uppercase animate-text-2" data-animation="jackInTheBox delay-1s">Many products</p>
                        <a class="p-uppercase button-animate" data-animation="jackInTheBox delay-2s"> Shop Now</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="hr-40"></div>

<div class="content" id="content">
    <div class="container">

        @if (!empty($products))
        @foreach ($products as $row)
        <div class="sec-title p-b-20" id="recent-product-title">
            <h3 class="m-text5 t-center">
                {{ $row['name'] }}
            </h3>
        </div>

        <div class="row product-row">
            @foreach ($row['data']['product'] as $product)
            <div class="col-5 product-col">
                <div class="product-col-inside-1">
                    <!-- product label -->
                    {!! IHelpers::displayLabel($product['product_label'], $row['data']['allLabel']) !!}
                    <!-- end product label -->
                    <div class="product-col-type-1">
                        <a href="{{ IHelpers::genSlug($product['slug']) }}">
                            <img src="{{ IHelpers::imageDisplaySlir('w190-h190', $product['image']) }}"
                                 alt="Avatar" class="product-image img-responsive">
                            <div class="overlay">

                            </div>
                        </a>
                        <div class="overlay-btn-cart {{$product['action_class']}}"
                             attr-name="{{$product['name']}}"
                             attr-qty="{{$product['qty']}}"
                             attr-qty-order="{{$product['qty_order']}}"
                             attr-image="{{$product['image']}}"
                             attr-price="{{$product['final_price']}}"
                             attr-origin-price="{{$product['final_price']}}"
                             attr-product-id="{{$product['id']}}"
                             attr-variant-id="0"
                             attr-slug="{{$product['slug']}}"
                             attr-weight="{{$product['weight']}}"
                             attr-weight-id="{{$product['weight_id']}}">
                            {{ $product['action_text'] }}
                        </div>
                    </div>
                    <div class="product-col-text-1 text-center">
                        <div class="product-text-name">
                            <a href="{{ IHelpers::genSlug($product['slug']) }}">
                                {{ $product['name'] }}
                            </a>
                        </div>
                        <div class="product-text-price">
                            @if ($product['display_price_promotion'] == $const['yes'])
                            <span class="price-previous">@displayPriceAndCurrency($product['price'])</span>
                            @displayPriceAndCurrency($product['final_price'])
                            @else
                            @displayPriceAndCurrency($product['final_price'])
                            @endif
                        </div>
                        <div class="add-to-cart-mobile">
                            <div class="add-to-cart-btn {{$product['action_class']}}"
                                 attr-name="{{$product['name']}}"
                                 attr-qty="{{$product['qty']}}"
                                 attr-qty-order="{{$product['qty_order']}}"
                                 attr-image="{{$product['image']}}"
                                 attr-price="{{$product['final_price']}}"
                                 attr-origin-price="{{$product['final_price']}}"
                                 attr-product-id="{{$product['id']}}"
                                 attr-variant-id="0"
                                 attr-slug="{{$product['slug']}}"
                                 attr-weight="{{$product['weight']}}"
                                 attr-weight-id="{{$product['weight_id']}}">
                                {{ $product['action_text'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="horizon-grey"></div>
        @endforeach
        @endif

        @if (!empty($category))
            @foreach ($category as $row)
                <div class="sec-title p-b-20" id="recent-product-title">
                    <h3 class="m-text5 t-center">
                        {{ $row['name'] }}
                    </h3>
                </div>

                <div class="row product-row">
                    @foreach ($row['data']['product'] as $product)
                    <div class="col-5 product-col">
                        <div class="product-col-inside-1">
                            <!-- product label -->
                            {!! IHelpers::displayLabel($product['product_label'], $row['data']['allLabel']) !!}
                            <!-- end product label -->
                            <div class="product-col-type-1">
                                <a href="{{ IHelpers::genSlug($product['slug']) }}">
                                    <img src="{{ IHelpers::imageDisplaySlir('w190-h190', $product['image']) }}"
                                         alt="Avatar" class="product-image img-responsive">
                                    <div class="overlay">

                                    </div>
                                </a>
                                <div class="overlay-btn-cart {{$product['action_class']}}"
                                     attr-name="{{$product['name']}}"
                                     attr-qty="{{$product['qty']}}"
                                     attr-qty-order="{{$product['qty_order']}}"
                                     attr-image="{{$product['image']}}"
                                     attr-price="{{$product['final_price']}}"
                                     attr-origin-price="{{$product['final_price']}}"
                                     attr-product-id="{{$product['id']}}"
                                     attr-variant-id="0"
                                     attr-slug="{{$product['slug']}}"
                                     attr-weight="{{$product['weight']}}"
                                     attr-weight-id="{{$product['weight_id']}}">
                                    {{ $product['action_text'] }}
                                </div>
                            </div>
                            <div class="product-col-text-1 text-center">
                                <div class="product-text-name">
                                    <a href="{{ IHelpers::genSlug($product['slug']) }}">
                                        {{ $product['name'] }}
                                    </a>
                                </div>
                                <div class="product-text-price">
                                    @if ($product['display_price_promotion'] == $const['yes'])
                                    <span class="price-previous">@displayPriceAndCurrency($product['price'])</span>
                                    @displayPriceAndCurrency($product['final_price'])
                                    @else
                                    @displayPriceAndCurrency($product['final_price'])
                                    @endif
                                </div>
                                <div class="add-to-cart-mobile">
                                    <div class="add-to-cart-btn {{$product['action_class']}}"
                                         attr-name="{{$product['name']}}"
                                         attr-qty="{{$product['qty']}}"
                                         attr-qty-order="{{$product['qty_order']}}"
                                         attr-image="{{$product['image']}}"
                                         attr-price="{{$product['final_price']}}"
                                         attr-origin-price="{{$product['final_price']}}"
                                         attr-product-id="{{$product['id']}}"
                                         attr-variant-id="0"
                                         attr-slug="{{$product['slug']}}"
                                         attr-weight="{{$product['weight']}}"
                                         attr-weight-id="{{$product['weight_id']}}">
                                        {{ $product['action_text'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="horizon-grey"></div>
            @endforeach
        @endif

        <div class="sec-title p-b-20" id="recent-product-title">
            <h3 class="m-text5 t-center">
                {{ Theme::lang('lang.home.trusted_partners') }}
            </h3>
        </div>

        <div class="row" id="brand-list">
            @foreach ($brand as $row)
                <div class="col-md-2 col-sm-4 col-xs-6 text-center">
                    <img src="{{ IHelpers::imageDisplaySlir('w190-h110', $row) }}" alt="Avatar" class="brand-image-homepage img-responsive">
                </div>
            @endforeach
        </div>

        <div class="row hr-40"></div>


    </div>
</div>
@stop