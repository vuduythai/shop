<div class="box-1">
    <p class="pull-left">{{ Theme::lang('lang.general.address') }}</p>
    <button type="button" class="btn btn-black modal-add-user-address pull-right"
            id="btn-add-address-in-checkout">
        {{ Theme::lang('lang.checkout.add_address') }}
    </button>
</div>
<div class="box-1-content">
    <h4>{{ Theme::lang('lang.checkout.billing_address') }}</h4>
    @if (!empty($userExtends))
        <select class="form-control select2" name="user_address_billing">
        @foreach ($userExtends as $address)
            <option value="{{$address['id']}}">{{ $address['address'] }}</option>
        @endforeach
        </select>
    @endif

<div class="hr-20"></div>
<input type="checkbox" name="use_same_address" id="use_same_address" value="0" checked/>
{{ Theme::lang('lang.checkout.same_address') }}
<div class="hr-10"></div>

<div id="div-address-shipping" class="{{$classHidden}}">
    <h4>{{ Theme::lang('lang.checkout.shipping_address') }}</h4>
    @if (!empty($userExtends))
        <select class="form-control select2" name="user_address_ship">
        @foreach ($userExtends as $address)
            <option value="{{$address['id']}}">{{ $address['address'] }}</option>
        @endforeach
        </select>
    @endif
</div>

</div>