@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.checkout') }}
@stop

@section('content')
<!-- include stripe gateway -->
<script src="https://checkout.stripe.com/checkout.js"></script>

<div id="const-json" style="display: none">{{ json_encode($const) }}</div>
<div id="currency-data" style="display: none">{{ json_encode($currency) }}</div>
<div id="currency_code" style="display: none">{{$currencyCode}}</div>
<!-- keep ship money and coupon-reduce to calculate total final price-->
<div id="ship-money" style="display: none">0</div>
<div id="coupon-reduce" style="display: none">0</div>
<input type="hidden" id="stripe_publish" value="{{$config['stripe_publish']}}" />
<input type="hidden" id="coupon_id" value="0" />
<input type="hidden" id="checkout_ok_url" value="{{$checkoutUrlOk}}" />
<input type="hidden" id="checkout_fail_url" value="{{$checkoutUrlFail}}" />
<!-- end keep ship money and coupon-reduce -->

@if (Auth::guard('users')->check())
    @php
    $user = Auth::guard('users')->getUser();
    $classHidden = 'class-hidden';
    @endphp
    <input type="hidden" id="user_id" value="{{$user['id']}}" />
    <input type="hidden" id="logged_in_email" value="{{$user['email']}}"/>
@else
    @php
    $classHidden = '';
    @endphp
    <input type="hidden" id="user_id" value="0" />
@endif

<div class="container">
    <div class="row content-div" id="checkout-div">
        <h1 class="text-center cart-title-h1">Shopping Cart</h1>
        <div class="col-md-5">
            <!-- Tab panes -->
            <div class="tab-content-checkout">
                <div class="tab-content" id="register" style="">
                    @if (Auth::guard('users')->check())
                    @include('partials.checkoutUserLogged')
                    @else
                    @include('partials.checkoutUserNotLogged')
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-12" id="checkout-cart-div">

                </div>
            </div>
            <div class="hr-20"></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="box-1">
                        <p>{{ Theme::lang('lang.checkout.shipping_method') }}</p>
                    </div>
                    <div class="box-1-content">
                        @php
                        $k = 1;
                        @endphp
                        @if (!empty($ship))
                            @foreach ($ship as $row)
                            <div class="box-radio">
                                <input type="radio" name="shipping_rule"
                                       attr-id="{{$row['id']}}"
                                       attr-type="{{$row['type']}}"
                                       attr-price-above="{{$row['above_price']}}"
                                       attr-cost="{{$row['cost']}}"
                                       attr-weight-type="{{$row['weight_type']}}"
                                       attr-weight-base="{{$row['weight_based']}}"
                                       class="shipping-method-type"
                                       {{ $k == 1 ? 'checked="checked"' : ''}}/>
                                <span>
                                    {{ $row['name'] }}
                                    @if ($row['cost'] != 0)
                                      - @displayPriceAndCurrency($row['cost'])
                                    @endif
                                </span>
                            </div>
                            @php
                            $k = $k + 1
                            @endphp
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box-1">
                        <p>{{ Theme::lang('lang.checkout.payment_method') }}</p>
                    </div>
                    <div class="box-1-content">
                        @php
                        $i = 1;
                        @endphp
                        @if (!empty($payment))
                            @foreach ($payment as $row)
                                <div class="box-radio">
                                    <input type="radio" name="payment_method" class="" value="{{ $row['code'] }}"
                                    {{ $i == 1 ? 'checked="checked"' : ''}}/><span>{{ $row['name'] }}</span>
                                </div>
                                @php
                                $i = $i + 1
                                @endphp
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="hr-20"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box-1">
                        <p>Comment</p>
                    </div>
                    <div class="box-1-content box-comment">
                        <div class="form-group">
                            <textarea class="form-control" name="comment"></textarea>
                        </div>

                    </div>
                </div>
            </div>
            <div class="hr-20"></div>
            <div class="row" id="term_and_condition_agree">
                <div class="col-md-12">
                    <label for="payment_address_agree" class="control-label">
                        <input type="hidden" name="payment_agree" value="0">
                        <input type="checkbox" autocomplete="off" name="payment_agree"
                               id="payment_agree" class="validate required" required="" value="1">
                        <span title=""> {{ Theme::lang('lang.checkout.i_have_read_and_agree') }}
                            <a href="" class="agree"><b>{{ Theme::lang('lang.checkout.privacy_policy') }} </b></a></span>
                    </label>
                    <br/>
                    <label for="confirm_agree" class="control-label">
                        <input type="hidden" name="confirm" value="0">
                        <input type="checkbox" autocomplete="off" name="confirm"
                               id="confirm_agree" class="validate required" required="" value="1">
                <span title="">{{ Theme::lang('lang.checkout.i_have_read_and_agree') }}
                    <a href="" class="agree">
                        <b>
                            {{ Theme::lang('lang.checkout.term_and_condition') }}
                        </b>
                    </a>
                </span>
                    </label>
                </div>
            </div>
            <div class="hr-20"></div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-black pull-right" id="checkout-button">
                        {{ Theme::lang('lang.checkout.checkout') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@include('partials.paypal')

@stop

@section('scripts')
<script src="{{ themes('js/checkout.js') }}"></script>
@stop