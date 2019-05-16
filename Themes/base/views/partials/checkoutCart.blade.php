<div class="flex-title d-flex flex-title-checkout">
    <div class="one-forth text-left one-forth-checkout">
        <span>{{ Theme::lang('lang.cart.product_detail') }}</span>
    </div>
    <div class="one-eight text-center one-eight-checkout">
        <span>{{ Theme::lang('lang.cart.price') }}</span>
    </div>
    <div class="one-eight text-center one-eight-checkout">
        <span>{{ Theme::lang('lang.cart.qty') }}</span>
    </div>
    <div class="one-eight text-center one-eight-checkout">
        <span>{{ Theme::lang('lang.cart.total') }}</span>
    </div>
    <div class="one-eight text-center one-eight-checkout">
        <span>{{ Theme::lang('lang.cart.remove') }}</span>
    </div>
</div>

@foreach ($cartDetail as $item)
    <div class="flex-cart d-flex flex-cart-checkout">
        <div class="one-forth one-forth-checkout">
            <img src="{{ IHelpers::imageDisplaySlir('w50-h50', $item['image']) }}"
                 class="product-img checkout-cart-img"/>
            <div class="display-tc">
                <h3>{{ $item['name'] }}</h3>
            </div>
        </div>
        <div class="one-eight text-center one-eight-checkout">
            <div class="display-tc">
                <span class="price">@displayPriceAndCurrency($item['price'])</span>
            </div>
        </div>
        <div class="one-eight text-center one-eight-checkout">
            <div class="display-tc">
                <input type="text"
                       attr-qty="{{ $item['qty_origin'] }}"
                       attr-qty-order="{{ $item['qty_order'] }}"
                       attr-name="{{ $item['name'] }}"
                       attr-image="{{ $item['image'] }}"
                       attr-price="{{ $item['price'] }}"
                       attr-product-id="{{ $item['id'] }}"
                       attr-variant-id="0"
                       attr-slug="{{$item['slug']}}"
                       attr-weight="{{$item['weight']}}"
                       attr-weight-id="{{$item['weight_id']}}"
                       class="form-control input-number text-center cart-qty-in-checkout"
                       value="{{ $item['qty'] }}"
                       min="1"
                       max="100">
                <div class="refresh-qty-in-checkout">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="one-eight text-center one-eight-checkout">
            <div class="display-tc">
                <span class="price">@displayPriceAndCurrency($item['total_price_per_item'])</span>
            </div>
        </div>
        <div class="one-eight text-center one-eight-checkout">
            <div class="display-tc">
                <a href="javascript:void()" class="closed cart-remove-item-detail"
                   attr-product-id="{{ $item['id'] }}">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach

<div class="checkout-cart-total row">
    <div class="pull-left checkout-cart-coupon col-md-9">
        <input type="text" name="coupon" id="coupon_code" class="form-control"
               placeholder="{{ Theme::lang('lang.checkout.enter_coupon') }}"/>
        <a href="javascript:void(0)" class="btn btn-black" id="apply-coupon">
            {{ Theme::lang('lang.checkout.apply') }}
        </a>
    </div>
    <div class="pull-right checkout-cart-total-info col-md-3">
        <div class="tr-checkout-total">
            <div class="checkout-address-label">
                {{ Theme::lang('lang.cart.sub_total') }} : @displayPriceAndCurrency($totalPrice)
            </div>
        </div>
        <div class="tr-checkout-total">
            <div class="checkout-address-label">
                {{ Theme::lang('lang.checkout.ship') }} : <span id="shipping-cost">0</span>
            </div>
        </div>
        <div class="tr-checkout-total">
            <div class="total-price">
                {{ Theme::lang('lang.cart.total') }} : <span id="total-price-span">@displayPriceAndCurrency($totalPrice)</span>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="total-price-hidden" value="{{$totalPrice}}" />
<input type="hidden" id="qty-total-hidden" value="{{$qtyTotal}}" />
<input type="hidden" id="weight-total-hidden" value="{{$weightTotal}}" />