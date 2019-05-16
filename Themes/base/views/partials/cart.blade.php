<div class="container">
    <div class="row content-div">
        <div class="col-md-12">
            <h1 class="text-center cart-title-h1">{{ Theme::lang('lang.cart.shopping_cart') }}</h1>
            <div class="flex-title d-flex">
                <div class="one-forth text-left">
                    <span>{{ Theme::lang('lang.cart.product_detail') }}</span>
                </div>
                <div class="one-eight text-center">
                    <span>{{ Theme::lang('lang.cart.price') }}</span>
                </div>
                <div class="one-eight text-center">
                    <span>{{ Theme::lang('lang.cart.qty') }}</span>
                </div>
                <div class="one-eight text-center">
                    <span>{{ Theme::lang('lang.cart.total') }}</span>
                </div>
                <div class="one-eight text-center">
                    <span>{{ Theme::lang('lang.cart.remove') }}</span>
                </div>
            </div>

            @if (!empty($cartDetail))
            @foreach ($cartDetail as $item)
                <div class="flex-cart d-flex">
                    <div class="one-forth">
                        <a href="{{ IHelpers::genSlug($item['slug']) }}">
                            <img src="@imageDisplay($item['image'])" class="product-img"/>
                        </a>
                        <div class="display-tc">
                            <h3>{{ $item['name'] }}</h3>
                        </div>
                    </div>
                    <div class="one-eight text-center">
                        <div class="display-tc">
                            <span class="price">@displayPriceAndCurrency($item['price'])</span>
                        </div>
                    </div>
                    <div class="one-eight text-center">
                        <div class="display-tc">
                            <input type="text" id="quantity"
                                   name="quantity"
                                   attr-qty="{{$item['qty_origin']}}"
                                   attr-qty-order="{{$item['qty_order']}}"
                                   attr-name="{{$item['name']}}"
                                   attr-image="{{$item['image']}}"
                                   attr-price="{{$item['price']}}"
                                   attr-product-id="{{$item['id']}}"
                                   attr-variant-id="0"
                                   attr-slug="{{$item['slug']}}"
                                   attr-weight="{{$item['weight']}}"
                                   attr-weight-id="{{$item['weight_id']}}"
                                   value="{{$item['qty']}}"
                                   min="1" max="100"
                                   class="form-control input-number text-center cart-qty">
                            <div class="refresh-qty">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <div class="one-eight text-center">
                        <div class="display-tc">
                            <span class="price">@displayPriceAndCurrency($item['total_price_per_item'])</span>
                        </div>
                    </div>
                    <div class="one-eight text-center">
                        <div class="display-tc">
                            <a href="javascript:void()" class="closed cart-remove-item-detail"
                               attr-product-id="{{ $item['id'] }}">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            @endif

        </div>
    </div>
    <div class="row content-div">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4" id="cart-summary">
            <div class="box-1">
                <p>{{ Theme::lang('lang.cart.summary') }}</p>
            </div>
            <div class="box-1-content">
                <div class="content-text">
                    <p>{{ Theme::lang('lang.cart.total') }} : </p><span>@displayPriceAndCurrency($totalPrice)</span>
                </div>
                <div class="content-text">
                    <a href="/checkout" class="btn btn-black" id="proceed-to-checkout">
                        {{ Theme::lang('lang.cart.proceed_to_checkout') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>