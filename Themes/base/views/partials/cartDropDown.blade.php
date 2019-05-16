<div class="dropdown-cart-title">
    <p><b>{{ Theme::lang('lang.cart.my_cart') }}</b></p>
</div>
@if (!empty($cart))
<div class="cart-dropdown-div">
    @foreach ($cart as $item)
    <div class="cart-dropdown-item">
        <div class="cart-dropdown-item-image">
            <a href="{{ IHelpers::genSlug($item['slug']) }}">
                <img src="{{ IHelpers::imageDisplaySlir('h50-w50',$item['image']) }}" width="50" height="50"/>
            </a>
        </div>
        <div class="cart-dropdown-item-name-qty">
            <div class="product-cart-name"><a href="{{$item['slug']}}">{{$item['name']}}</a></div>
            <div class="product-money-qty">{{ $item['qty'] }} x @displayPriceAndCurrency($item['price']) </div>
        </div>
        <div  class="cart-dropdown-item-remove">
            <a href="javascript:void(0)" class="cart-remove-item-detail" attr-product-id="{{$item['id']}}" >
                <i class="fa fa-times" aria-hidden="true">
                </i>
            </a>
        </div>
    </div>
    @endforeach
</div>

<div class="text-center" id="cart-dropdown-subtotal">
    <b>{{ Theme::lang('lang.cart.sub_total') }}</b> :
    @displayPriceAndCurrency($totalPrice)
</div>

<a href="/cart" class="btn btn-default">{{ Theme::lang('lang.cart.view_cart') }}</a>
<a href="/checkout" class="btn btn-black pull-right">{{ Theme::lang('lang.cart.checkout') }}</a>

@else
No item
@endif
