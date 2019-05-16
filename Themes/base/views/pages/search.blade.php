@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.search') }}
@stop

@section('content')

<div class="container">
    <div class="row">
        <h3 class="text-center">{{ Theme::lang('lang.general.search_result') }}</h3>
    </div>
    <div class="row" id="search-result">
        @foreach ($products as $product)
        <div class="col-5 product-col">
            <div class="product-col-inside-1">
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
                    <div class="product-text-price">@displayPriceAndCurrency($product['price'])</div>
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
    <div class="row">
        <div class="text-center">
            <div class="pagination">
                @php
                $link = '/search?key='.$key.'&page=';
                @endphp

                @if ($products->currentPage() != 1)
                <a href="{{ URL::to($link.'1') }}"> << </a>
                <a href="{{ URL::to($link.($products->currentPage() - 1)) }}"> < </a>
                @endif

                @if ($products->lastPage() != 1)
                @for ($i = 1; $i <= $products->lastPage() ; $i++)
                <a href="{{ URL::to($link.$i) }}"
                   class="{{ $products->currentPage() == $i ? 'active' : ''}}"
                   attr-page="{{$i}}">{{$i}}
                </a>
                @endfor
                @endif

                @if ($products->currentPage() != $products->lastPage())
                <a href="{{ URL::to($link.($products->currentPage() + 1)) }}"> >> </a>
                <a href="{{ URL::to($link.$products->lastPage()) }}"> > </a>
                @endif
            </div>
        </div>
    </div>
</div>

@stop
