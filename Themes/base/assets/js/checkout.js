/**
 * Default payment methods : cod, paypal, stripe
 * Calculate separate ship and coupon based on totalPrice
 * then assign to div #ship-money and #coupon-reduce
 * finally: $.assignTotalPriceSpanDiv()
 * (tax : calculate before add-to-cart event)
 * There are two events: 'beforeSaveOrder' and 'afterSaveOrder' for another payment method
 */
//find closest number
function closest (num, arr) {
    var mid;
    var lo = 0;
    var hi = arr.length - 1;
    while (hi - lo > 1) {
        mid = Math.floor ((lo + hi) / 2);
        if (arr[mid] < num) {
            lo = mid;
        } else {
            hi = mid;
        }
    }
    if (num - arr[lo] <= arr[hi] - num) {
        return arr[lo];
    }
    return arr[hi];
}

//generate random string
function generateRandomString(length) {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789";
    for (var i = 0; i < parseInt(length); i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}

$(document).ready(function() {

    var baseUrl = window.location.origin;
    var tokenGenerate = $('#token_generate').val();
    var totalPriceSpanDiv = 'total-price-span';
    var totalPriceHiddenDiv = 'total-price-hidden';
    var shipMoneyDiv = 'ship-money';
    var couponReduceDiv = 'coupon-reduce';
    var waitMeColor = '#fe4c50';
    var msgJsJson = $('#msg_js').text();
    var msgJsObj = JSON.parse(msgJsJson);

    var constJson = $('#const-json').text();
    var constObj = JSON.parse(constJson);
    var TYPE_PRICE = constObj.ship_type_price;
    var TYPE_GEO = constObj.ship_type_geo;
    var TYPE_WEIGHT_BASED = constObj.ship_type_weight_based;
    var TYPE_PER_ITEM = constObj.ship_type_per_item;
    var TYPE_GEO_WEIGHT_BASED = constObj.ship_type_geo_weight_based;
    var WEIGHT_TYPE_FIXED = constObj.weight_type_fixed;
    var CASH_ON_DELIVERY = constObj.cod_method;
    var PAYPAL = constObj.paypal_method;
    var STRIPE = constObj.stripe_method;
    var SUCCESS = constObj.rs_success;
    var FAIL = constObj.rs_fail;
    var userId = $('#user_id').val();

    jQuery.getTotalPriceFinal = function() {
        var totalPrice = $('#'+totalPriceHiddenDiv).val();
        var couponMoney = parseFloat($('#'+couponReduceDiv).text());
        var priceShip = parseFloat($('#'+shipMoneyDiv).text());
        return parseFloat(totalPrice) + priceShip - couponMoney;
    };

    jQuery.assignTotalPriceSpanDiv = function() {
        var total = $.getTotalPriceFinal();
        $('#'+totalPriceSpanDiv).text($.displayCurrency(total));
    };

    jQuery.calculateShipPrice = function(thisDiv) {
        var type = thisDiv.attr('attr-type');
        var cost = thisDiv.attr('attr-cost');
        var priceShip = 0;
        if (type == TYPE_PRICE || type == TYPE_GEO) {
            priceShip = parseFloat(cost);
        }
        if (type == TYPE_PER_ITEM) {
            var qtyTotal = $('#qty-total-hidden').val();
            priceShip = parseFloat(cost) * qtyTotal;
        }
        if (type == TYPE_WEIGHT_BASED || type == TYPE_GEO_WEIGHT_BASED) {
            var weightTotal = $('#weight-total').text();
            var weightBased = thisDiv.attr('attr-weight-base');
            var weightType = thisDiv.attr('attr-weight-type');
            var weightTotalCeil = parseInt(Math.ceil(weightTotal));
            if (weightType == WEIGHT_TYPE_FIXED) {//fixed
                var weightRateFix = weightBased.split(':');
                var eachUnit = parseInt(weightRateFix[0]);
                var unitPrice = parseFloat(weightRateFix[1]);
                priceShip = (weightTotalCeil / eachUnit) * unitPrice;
            } else {//rate
                var weightRateRate = weightBased.split(',');
                var weightRateArray = [];
                var weightRateArrayIndex = [];
                for (var i=0; i<weightRateRate.length; i++) {
                    var weightRateSplit = weightRateRate[i].split(':');
                    weightRateArray[weightRateSplit[0]] = weightRateSplit[1];
                    weightRateArrayIndex.push(weightRateSplit[0]);
                }
                //find closet lowest in array
                var indexClosest = closest(weightTotalCeil, weightRateArrayIndex);
                priceShip = weightRateArray[indexClosest];
            }
        }
        $('#'+shipMoneyDiv).text(priceShip);
        var textShippingCost = $.displayCurrency(priceShip);
        $('#shipping-cost').text(textShippingCost);
        $.assignTotalPriceSpanDiv();
    };

    //find #checkout-cart-div to fill data for cart
    var cartRs = sessionStorage.getItem('cart');
    var cartRsObj = JSON.parse(cartRs);
    cartRsObj._token = tokenGenerate;
    $.callAjaxHtml('post', '/onAjaxCartCheckout', cartRsObj, $('#checkout-cart-div'), function(res) {
        $('#checkout-cart-div').html(res);
        //initial ship
        $('.shipping-method-type').each(function() {
            if ($(this).is(':checked')) {
                $.calculateShipPrice($(this));
            }
        });
    });

    /**
     * Choose shipping method
     */
    $('.shipping-method-type').click(function() {
        $.calculateShipPrice($(this))
    });

    //Handle coupon code
    $(document).on('click', '#apply-coupon', function() {
        var coupon = $('#coupon_code').val();
        var cartRs = sessionStorage.getItem('cart');
        var cartRsObj = JSON.parse(cartRs);
        var totalPrice = $('#'+totalPriceHiddenDiv).val();
        var params = {};
        params.cart = cartRsObj;
        params.coupon = coupon;
        params.totalPrice = totalPrice;
        params._token = tokenGenerate;
        $.callAjax('post', '/onCheckCoupon', params, $('#checkout-cart-div'), function(res) {
            if (res.rs == false) {
                $.displayMsg(res.msg, 'danger', 1000);
            } else {
                $('#'+couponReduceDiv).text(totalPrice - res.discount_price);
                $('#coupon_id').val(res.coupon_id);
                $.assignTotalPriceSpanDiv();
            }
        });
    });

    //Checkout by paypal
    jQuery.checkoutPaypal = function(params, orderId) {
        var urlSuccessInput = $('#url_success');
        var numberRandom = generateRandomString(10);
        numberRandom = numberRandom + '_' + orderId;
        sessionStorage.setItem('token_paypal', numberRandom);//store token
        sessionStorage.setItem('order_info', params);
        var urlSuccess = urlSuccessInput.val();
        //set token to check if paid by paypal
        urlSuccessInput.val(urlSuccess+'?token='+numberRandom);
        var cartRs = sessionStorage.getItem('cart');
        var cartObj = JSON.parse(cartRs);
        var nameItems = '';
        $.each(cartObj, function(index, value){
            nameItems += value.name+' x '+value.qty+', ';
        });
        nameItems = nameItems.substring(0, nameItems.length - 2);//remove last ', '
        //assign paypal information to checkout
        $('#paypal_item_name').val('Order Id : '+ orderId);
        $('#paypal_item_qty').val($('#qty-total-hidden').val());
        $('#total_amount_paypal').val($.getTotalPriceFinal());
        $('#currency_code_paypal').val($('#currency_code').text());
        $('#checkout-div').waitMe({color: waitMeColor});
        $('#paypal_method_form').submit();
    };

    //checkout stripe
    jQuery.checkoutStripe = function(params) {
        var handler = StripeCheckout.configure({
            key: $('#stripe_publish').val(),//public key
            image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
            locale: 'auto',
            token: function(token) {
                var data = {};
                data.tokenVar = token.id;
                data.params = params;
                data._token = tokenGenerate;
                $.callAjax('post', '/onCheckOutStripe', data, $('#checkout-cart-div'), function(res) {
                    $('#checkout-div').waitMe({color: waitMeColor});
                    if (res == SUCCESS) {
                        sessionStorage.setItem('cart', '');
                        window.location.href = baseUrl+'/'+$('#checkout_ok_url').val();
                    } else {
                        $.displayMsg(res, 'danger', 3);
                    }
                });
            }
        });
        var customerEmail = '';
        if (userId != 0) {
            customerEmail = $('#logged_in_email').val();
        } else {
            customerEmail = $('#billing_email').val();
        }
        handler.open({
            name: msgJsJson.checkout_stripe,
            description: msgJsObj.checkout_stripe_gateway,
            amount: $.getTotalPriceFinal() * 100,
            currency: $('#currency_code').text(),
            email: customerEmail,
            image: $('#stripe_logo').val()
        });
    };


    //Save order
    jQuery.saveOrder = function(params) {
        $.callAjax('post', '/onSaveOrder', params, $('#checkout-div'), function(orderId) {
            if (orderId != FAIL) {
                if (params.payment_method == CASH_ON_DELIVERY) {
                    sessionStorage.setItem('cart', '');
                    window.location.href = baseUrl+'/'+$('#checkout_ok_url').val();
                    return;
                }
                if (params.payment_method == PAYPAL) {
                    $.checkoutPaypal(params, orderId);
                    sessionStorage.setItem('cart', '');
                    return;
                }
                // create hook for after save order  for another payment
                $(document).trigger( "afterSaveOrder", {params:params, orderId:orderId} );
            } else {
                window.location.href = baseUrl+'/'+$('#checkout_fail_url').val();
            }
        });
    };

    $('#use_same_address_not_login').click(function() {
        if (!$(this).is(':checked')) {
            $('#guest-shipping-detail').removeClass('class-hidden');
        } else {
            $('#guest-shipping-detail').addClass('class-hidden');
        }
    });

    $('#use_same_address').click(function() {
        if ($(this).is(':checked')) {
            $('#div-address-shipping').hide();
        } else {
            $('#div-address-shipping').removeClass('class-hidden').show();
        }
    });

    //checkout
    $('#checkout-button').click(function() {
        if (!$('#payment_agree').is(':checked')) {
            $.displayMsg(msgJsObj.have_to_agree_privacy_policy, 'danger', 3);
            return;
        }
        if (!$('#confirm_agree').is(':checked')) {
            $.displayMsg(msgJsObj.have_to_agree_term, 'danger', 3);
            return;
        }
        var params = {};
        params._token = tokenGenerate;
        if (userId != 0) {
            //if login
            var addressBilling = $('select[name="user_address_billing"]').val();
            var addressShipping = 0;
            if (!$('input[name="use_same_address"]').is(':checked')) {
                addressShipping = $('select[name="user_address_ship"]').val();
            } else {
                addressShipping = addressBilling;
            }
            if (addressBilling == undefined || addressShipping == undefined) {
                $.displayMsg(msgJsObj.have_billing_address, 'danger', 3);
                return;
            }
            params.address_billing = addressBilling;
            params.address_shipping = addressShipping;
        } else {
            params.form_address_not_login_in = $.convertFormData($('#form-address-in-checkout').serializeArray());
            $.callAjax('post', '/onValidateAddressNotLogin', params, $('#checkout-div'), function(res) {
                if (res.rs == $('#fail-val').val()) {
                    $.displayMsg(res.msg[0], 'danger', 1000);
                } else {
                    //if validate successful (waitMe will hide) continue to run waitMe
                    $('#checkout-div').waitMe({color:waitMeColor});
                }
            });
        }
        params.user_id = userId;
        params.shipping_cost = parseFloat($('#'+shipMoneyDiv).text());
        params.total = $.getTotalPriceFinal();
        var cartRs = sessionStorage.getItem('cart');
        params.cart = JSON.parse(cartRs);
        params.payment_method = $('input[name="payment_method"]:checked').val();
        params.shipping_rule_id = $('input[name="shipping_rule"]:checked').attr('attr-id');
        params.currency_code = $('#currency_code').text();
        params.coupon_id = $('#coupon_id').val();
        params.coupon_total = $('#coupon-reduce').text();
        params.comment = $('#comment').val();
        sessionStorage.setItem('orderParams', JSON.stringify(params));
        //just save order if it not stripe checkout
        if (params.payment_method == STRIPE) {
            $.checkoutStripe(params);
        } else if (params.payment_method == CASH_ON_DELIVERY
            || params.payment_method == PAYPAL) {
            $('#checkout-div').waitMe({color: waitMeColor});
            $.saveOrder(params);
        } else {
            // create hook for before save order for another payment
            $(document).trigger( "beforeSaveOrder", {params:params} );
        }
    });

    $('#checkout-login-form-btn').click(function() {
        var params = {};
        params._token = tokenGenerate;
        $.callAjaxHtml('post', '/onModalLogin', params, $('#checkout-div'), function(res) {
            var fillModal = $('#divFillModal');
            fillModal.html(res);
            fillModal.modal();
        });
    });

    $(document).on('keypress', '.cart-qty-in-checkout', function(e) {
        if(e.which == 13){
            e.preventDefault();//Enter key pressed
            $.updateCartItem($(this));
            location.reload();
        }
    });

    $(document).on('click', '.refresh-qty-in-checkout', function(e) {
        e.preventDefault();//Enter key pressed
        $.updateCartItem($(this).prev());
        location.reload();
    });

});
