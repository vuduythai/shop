@extends('layouts.main')

@section('title')
@if (!empty($product))
{{$product['name']}}
@endif
@stop

@section('content')
@inject('frontend', 'Modules\Frontend\Classes\Frontend')
@inject('productComponent', 'Modules\Frontend\Components\Product')

@if (!empty($product))
<div id="const-json" style="display: none">{{ json_encode($const) }}</div>
<input type="hidden" id="separate-string" value="{{ $const['separate'] }}" />
<div id="value-type" style="display: none">{{ json_encode($valueType) }}</div>
<input type="hidden" id="fixed_amount" value="{{ $const['fixed_amount'] }}" />
<input type="hidden" id="yes_const" value="{{ $const['yes'] }}" />
<input type="hidden" id="is_variant_change_image" value="{{ $product['is_variant_change_image'] }}" />
<input type="hidden" id="product_id" value="{{ $product['id'] }}" />
<input type="hidden" id="product-type" value="{{ $product['product_type'] }}" />
<input type="hidden" id="product-type-config" value="{{ \Modules\Backend\Core\System::PRODUCT_TYPE_CONFIGURABLE }}" />
<input type="hidden" id="btn-buy-now-detail-name" value="buy-now-detail-span" />
<div id="currency-data" style="display: none">{{ json_encode($currency) }}</div>
<input type="hidden" id="folder-image" value="{{  \Modules\Backend\Core\System::FOLDER_IMAGE }}" />

<div class="container">
    <div class="row content-div">
        <div class="col-md-6">
            <!-- big image : src: smaller image, xoriginal : origin image -->
            <!-- thumb image : src: smaller image, href : origin image -->
            <div id="product-gallery">
                <img class="xzoom" id="xzoom-big-image"
                     src="{{ IHelpers::imageDisplaySlir('w555-h450', $product['image']) }}"
                     xoriginal="@imageDisplay($product['image'])" />
                @if (!empty($gallery))
                <ul class="xzoom-thumbs" id="detail-product-thumb">
                    <li>
                        <a href="@imageDisplay($product['image'])">
                            <img class="xzoom xzoom-thumbnail"
                                 src="{{ IHelpers::imageDisplaySlir('w555-h450', $product['image']) }}">
                        </a>
                    </li>
                    @foreach ($gallery as $image)
                    <li>
                        <a href="@imageDisplay($image)">
                            <img class="xzoom xzoom-thumbnail"
                                 src="{{ IHelpers::imageDisplaySlir('w555-h450', $image) }}">
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div id="product-desc">
                <div class="product-desc-div">
                    <h3 class="product-name">{{$product['name']}}</h3>
                    <p class="product-price product-attr-row">
                        @if ($product['display_price_promotion'] == $const['yes'])
                            <span class="price-previous">@displayPriceAndCurrency($product['price'])</span>
                            <span id="product-price-final-price">
                             @displayPriceAndCurrency($product['final_price'])
                            </span>
                        @else
                             <span id="product-price-final-price">
                             @displayPriceAndCurrency($product['final_price'])
                            </span>
                        @endif
                    </p>
                    <p id="product-price-detail">
                        {{ Theme::lang('lang.detail.product_price_detail') }} :
                        @displayPriceAndCurrency($product['final_price'])
                        <span id="product-price-detail-variant"></span>
                        <span id="product-price-detail-option"></span>
                    </p>
                    <p class="product-review product-attr-row">
                        {!! IHelpers::displayReviewStar($product['review_point']) !!} ({{$product['review_count']}} Reviews)
                    </p>
                    <p class="product-attr-row">
                       {{ $product['short_intro'] }}
                    </p>
                </div>

                @if (!empty($variant))
                <div id="variant-div">
                    <div id="variant-data" style="display: none;">{{ json_encode($variant['childConvert']) }}</div>
                    @php
                    $i = 1;
                    @endphp
                    @foreach ($variant['filter'] as $f)
                        <div class="product-detail-filter-name name-level-{{$i}}">{{$f['attribute_name']}}</div>
                        @foreach ($f['property'] as $p)
                            @if ($p['type'] == $const['attribute_type_color'])
                                <div id="property-{{$p['id']}}" property-id="{{$p['id']}}"
                                     attr-next="{{$p['property_next']}}"  attr-level="{{$i}}"
                                     class="property-detail level-{{$i}} property-color"
                                     style="background-color: {{$p['value']}}"></div>
                            @else
                                <div id="property-{{$p['id']}}" property-id="{{$p['id']}}"
                                     attr-next="{{$p['property_next']}}"  attr-level="{{$i}}"
                                     class="property-detail level-{{$i}} property-text">
                                    <span>{{$p['name']}}</span></div>
                            @endif
                        @endforeach
                    @php
                    $i = 1+1;
                    @endphp
                    @endforeach
                </div><!-- end #variant-div -->
                @endif
                
                @if (!empty($option))
                <div id="option-div">
                    @foreach ($option as $key => $o)
                       @php
                       $valueData = $frontend::convertOptionToForm($o['value']);
                       @endphp
                       <div class="form-group">
                           <label>{{ $o['name'] }}</label>
                           @if ($o['type'] == $const['option_type_select'])
                               {{Form::select('option-'.$o['id'], $valueData, '',
                                   ['class'=>'form-control select2 option-select', 'attr-option-id'=>$o['id']])}}
                           @elseif ($o['type'] == $const['option_type_radio'])
                               @foreach ($o['value'] as $row)
                                   @php
                                   $valueAddMore = $frontend::valueAddMoreText($row['value_type'], $row['value_price'], $row['value_name'])
                                   @endphp
                                   <div class="radio">
                                       <label>
                                           <input type="radio" name="option-{{ $o['id'] }}" class="option-choose"
                                                  attr-type="{{ $row['value_type'] }}" attr-option-id="{{ $o['id'] }}"
                                                  value="{{ $row['value_id'] }}">{{ $valueAddMore }}
                                       </label>
                                   </div>
                               @endforeach
                           @elseif ($o['type'] == $const['option_type_checkbox'])
                               @foreach ($o['value'] as $row)
                                   @php
                                   $valueAddMore = $frontend::valueAddMoreText($row['value_type'], $row['value_price'], $row['value_name'])
                                   @endphp
                                   <div class="checkbox">
                                       <label>
                                           <input type="checkbox" name="option-{{ $o['id'] }}" class="option-choose"
                                                  attr-type="{{ $row['value_type'] }}" attr-option-id="{{ $o['id'] }}"
                                                  value="{{ $row['value_id'] }}">{{ $valueAddMore }}
                                       </label>
                                   </div>
                               @endforeach
                           @else
                           <!-- multiple select -->
                           {{Form::select('option-'.$o['id'].'[]', $valueData, '',
                               ['class'=>'form-control select2 option-multi-select', 'attr-option-id'=>$o['id'], 'multiple'=>'multiple'])}}
                           @endif
                       </div>
                    @endforeach
                </div><!-- end #option-div -->
                @endif

                @php
                if ($product['is_out_of_stock'] == $const['is_out_of_stock'] ||
                    $product['product_type'] == $const['type_product_configurable']) {
                    $classHidden = 'class-hidden';
                } else {
                    $classHidden = '';
                }
                @endphp
            </div>

            @if ($product['is_in_stock'] == $const['in_stock'])
            <div class="input-qty-cart row product-attr-row {{$classHidden}}" id="qty-detail-div">
                <div id="input-qty" class="col-md-4">
                    <div class="product-icon-minus"><i class="fa fa-minus subtract-product-qty" aria-hidden="true"></i></div>
                    <div class="form-group input-number">
                        <input type="number" id="quantity" name="quantity"
                               class="form-control" value="1" min="1" max="100">
                    </div>
                    <div class="product-icon-plus"><i class="fa fa-plus add-product-qty" aria-hidden="true"></i></div>
                </div>
                <div id="add-to-cart" class="col-md-4">
                    <a href="javascript:void(0)" class="btn btn-black buy-now-detail btn-addtocart"
                       id="buy-now-detail-span"
                       attr-name="{{$product['name']}}"
                       attr-qty="{{$product['qty']}}"
                       attr-qty-order="{{$product['qty_order']}}"
                       attr-image="{{$product['image']}}"
                       attr-price="{{$product['final_price']}}"
                       attr-price-variant=""
                       attr-origin-price="{{$product['final_price']}}"
                       attr-product-id="{{$product['id']}}"
                       attr-variant-id="0"
                       attr-slug="{{$product['slug']}}"
                       attr-weight="{{$product['weight']}}"
                       attr-weight-id="{{$product['weight_id']}}"
                        >
                        <i class="icon-shopping-cart"></i>
                        {{ Theme::lang('lang.cart.add_to_cart') }}
                    </a>
                    <div id="order-option-json" style="display: none"></div>
                </div>
            </div>
            @else
            <p class="text-red">{{ Theme::lang('lang.cart.out_of_stock') }}</p>
            @endif

        </div>
    </div>

    <div class="row">
        <div class="product-group-tabs">
            <!-- Nav tabs -->
            <div id="nav-tabs-outside">
                <ul class="product-nav-tabs">
                    <li class="product-detail-tab">
                        <a href="javascript:void(0)" class="nav-tabs-tab active" attr-id="introduction">
                            {{ Theme::lang('lang.general.introduction') }}
                        </a>
                    </li>
                    <li class="product-detail-tab">
                        <a href="javascript:void(0)" class="nav-tabs-tab" attr-id="attribute">
                            {{ Theme::lang('lang.general.specification') }}
                        </a>
                    </li>
                    <li class="product-detail-tab">
                        <a href="javascript:void(0)" class="nav-tabs-tab" attr-id="reviews">
                            {{ Theme::lang('lang.general.reviews') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tab panes -->
            <div class="tab-content-div">
                <div class="tab-content active" id="introduction">
                    {!! $product['full_intro'] !!}
                </div>
                <div class="tab-content" id="attribute">
                    @if (!empty($attribute))
                    @foreach ($attribute as $key => $value)
                    <div class="row product-attribute-row">
                        <div class="col-md-2">
                            <span class="product-attribute-group">{{ $value['name'] }}</span>
                        </div>
                        <div class="col-md-10">
                            @foreach ($value['attribute'] as $attributeId => $attributeValue)
                            <div class="product-attribute-value">
                                <b>{{ $attributeValue['name'] }}</b> : {{ $attributeValue['value'] }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="tab-content" id="reviews">
                    <a href="javascript:void(0)" id="modal-review" attr-product-id="{{$product['id']}}">
                        @if ($review_permission == $const['yes'])
                        {{ Theme::lang('lang.review.write_review') }}
                        @endif
                    </a>
                    <div id="review-page">
                        @include('partials.reviewDisplay', ['review'=>$review])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sec-title p-b-20">
        <h3 class="m-text5 t-center">
            {{ Theme::lang('lang.general.related_product') }}
        </h3>
    </div>

    <div class="row product-row">
        @php
        $related = $productComponent::getRelatedProduct($product['id']);
        @endphp
        @foreach ($related['product'] as $product)
        <div class="col-5 product-col">
            <div class="product-col-inside-1">
                {!! IHelpers::displayLabel($product['product_label'], $related['allLabel']) !!}
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
</div>

@endif

@stop

@section('scripts')
<script src="{{ themes('js/detail.js') }}"></script>
@stop