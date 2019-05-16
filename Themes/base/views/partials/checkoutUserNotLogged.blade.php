<div class="box-1">
    <p class="pull-left">{{ Theme::lang('lang.general.address') }}</p>
    <button type="button" class="btn btn-black pull-right" id="checkout-login-form-btn">
        {{ Theme::lang('lang.general.login') }}
    </button>
</div>
<div class="box-1-content">
    {{Form::open(['url'=>'/', 'id'=>'form-address-in-checkout'])}}
    <div class="row">
        <div class="col-md-6">
            <!-- First Name -->
            <div class="form-group">
                <label class="checkout-address-label"><span class="required">*</span> {{ Theme::lang('lang.user.first_name') }}</label>
                <input type="text" name="billing_first_name" id="billing_first_name" class="form-control checkout-address-input"/>
            </div>
            <!-- Email -->
            <div class="form-group">
                <label class="checkout-address-label"><span class="required">*</span> {{ Theme::lang('lang.user.email') }}</label>
                <input type="text" name="billing_email" id="billing_email" class="form-control checkout-address-input" />
            </div>
        </div>
        <div class="col-md-6">
            <!-- Last Name -->
            <div class="form-group">
                <label class="checkout-address-label"><span class="required">*</span> {{ Theme::lang('lang.user.last_name') }}</label>
                <input type="text" name="billing_last_name" id="billing_last_name" class="form-control checkout-address-input" />
            </div>
            <!-- Telephone -->
            <div class="form-group">
                <label class="checkout-address-label"><span class="required">*</span> {{ Theme::lang('lang.user.phone') }}</label>
                <input type="number" name="billing_phone" id="billing_phone" class="form-control checkout-address-input" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Address-->
            <label class="checkout-address-label"><span class="required">*</span> {{ Theme::lang('lang.user.address') }}</label>
            <input type="text" name="billing_address" id="billing_address" class="form-control checkout-address-input"/>
        </div>
    </div>
    <div class="row" id="is_ship_same_address">
        <div class="col-md-12">
            <input type="checkbox" name="use_same_address_not_login" id="use_same_address_not_login" checked/>
            {{ Theme::lang('lang.checkout.same_address') }}
        </div>
    </div>
    <div class="class-hidden" id="guest-shipping-detail">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="checkout-address-label"><span class="required">*</span>  {{ Theme::lang('lang.user.first_name') }}</label>
                    <input type="text" name="shipping_first_name" id="shipping_first_name" class="form-control checkout-address-input"/>
                </div>
                <div class="form-group">
                    <label class="checkout-address-label"><span class="required">*</span>  {{ Theme::lang('lang.user.email') }}</label>
                    <input type="text" name="shipping_email" id="shipping_email"  class="form-control checkout-address-input" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="checkout-address-label"><span class="required">*</span>  {{ Theme::lang('lang.user.last_name') }}</label>
                    <input type="text" name="shipping_last_name" id="shipping_last_name"  class="form-control checkout-address-input" />
                </div>
                <div class="form-group">
                    <label class="checkout-address-label"><span class="required">*</span>  {{ Theme::lang('lang.user.phone') }}</label>
                    <input type="text" name="shipping_phone"  id="shipping_phone" class="form-control checkout-address-input" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="checkout-address-label"><span class="required">*</span>  {{ Theme::lang('lang.user.address') }}</label>
                <input type="text" name="shipping_address"  id="shipping_address" class="form-control checkout-address-input"/>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
