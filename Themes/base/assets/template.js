/*Sticky nav*/
window.onscroll = function() {addSticky()};
var navbar = document.getElementById("wrap-header-top");
var topHeadHeight = navbar.offsetHeight;
var sticky = navbar.offsetTop + topHeadHeight;
var headerMobile = document.getElementById('header-mobile');
function addSticky() {
    if (window.pageYOffset>= sticky) {
        navbar.classList.add("sticky");
        headerMobile.classList.add('sticky');
        $('#top-header').hide();
    } else {
        navbar.classList.remove("sticky");
        headerMobile.classList.remove('sticky');
        $('#top-header').show();
    }
}

$(document).ready(function(){
    var waitMeColor = '#fe4c50';
    var baseUrl = window.location.origin;
    var FAIL = $('#fail-val').val();
    /**
     * Function to call ajax
     */
    jQuery.callAjax = function(method, url, data, divWaitMe, callback) {
        $.ajax({
            method: method,
            url: url,
            data : data,
            beforeSend: function() {
                divWaitMe.waitMe({color: waitMeColor});
            },
            success: function(response) {
                divWaitMe.waitMe('hide');
                if (typeof callback == 'function') {
                    callback.call(this, response);//pass response to callback
                }
            }
        });
    };

    /**
     * Function to call ajax html
     */
    jQuery.callAjaxHtml = function(method, url, data, divWaitMe, callback) {
        $.ajax({
            method: method,
            url: url,
            data : data,
            dataType: 'html',
            beforeSend: function() {
                divWaitMe.waitMe({color: waitMeColor});
            },
            success: function(response) {
                divWaitMe.waitMe('hide');
                if (typeof callback == 'function') {
                    callback.call(this, response);//pass response to callback
                }
            }
        });
    };

    //convert form data
    jQuery.convertFormData = function(formData) {
        var data = {};
        $(formData).each(function(index, obj){
            if (obj.name.match(/\[+(.*?)\]+/g)) {
                var propertyName = obj.name.substring(0, obj.name.length - 2);//remove [] from name
                if (!data[propertyName]) {
                    //fix error 'cannot read property 'push' of undefined'
                    //initial for first element of data[propertyName] ~ define data[propertyName]
                    data[propertyName] = [];
                }
                //if data[propertyName] is defined, just push element to this
                data[propertyName].push(obj.value);
            } else {
                data[obj.name] = obj.value;
            }
        });
        return data;
    };

    //validate form dynamic create and update
    jQuery.validateThenSaveFrontend = function(e, action, form) {
        //create event beforeClickButtonSaveDataFrontend
        $(document).trigger('beforeClickButtonSaveDataFrontend');
        e.preventDefault();
        var formData = $.convertFormData($('#'+form).serializeArray());
        $.callAjax('post', '/'+action, formData, $('.form-div'), function(res) {
            if (res.rs == $('#fail-val').val()) {
                $.displayMsg(res.msg[0], 'danger', 1000);
            } else {
                window.location.href = res.redirect_url;
            }
        });
    };

    $(document).on('click', '.btn_save', function(e) {
        var action = $(this).attr('attr-form-action');
        var form = $(this).attr('attr-form');
        $.validateThenSaveFrontend(e, action, form);
    });

    if (document.getElementById('notify_flash_msg') != null) {
        $.displayMsg($('#notify_flash_msg').text(), 'success', 1000);
    }

    $('.select2').select2({width: '100%'});
    var headerMobile = $('#header-mobile');

    /* Show menu mobile */
    $('.btn-show-menu-mobile').on('click', function(){
        $(this).toggleClass('is-active');
        $('.wrap-side-menu').slideToggle();
        $("#wrapper").toggleClass("open");//add open to push content to right
        if ($(this).hasClass('is-active')) {
            $('.has-sub').each(function() {
                var text = $(this).text();
                if (text.trim() === '') {
                    $(this).remove()
                }
            });
        }
    });

    $(window).resize(function(){
        if($(window).width() >= 992){//hidden mobile menu when window> 992px
            $("#wrapper").removeClass("open");
            $('#sidebar-mobile').css('display', 'none');
            $('#search-input-mobile').css('display', 'none');
        } else {
            $('#sidebar-mobile').css('display', 'block');

        }
    });

    /*multilevel accodion*/
    $(".has-sub").click(function(e) {
        e.stopPropagation();
        var link = $(this);
        if (link.hasClass('active')) {
            link.children().closest('.fa').removeClass('fa-angle-down').addClass('fa-angle-right');
        } else {
            link.children().closest('.fa').removeClass('fa-angle-right').addClass('fa-angle-down');
        }
        var closest_ul = link.closest("ul");
        var parallel_active_links = closest_ul.find(".active");
        var closest_li = link.closest("li");
        var link_status = closest_li.hasClass("active");
        var count = 0;

        closest_ul.find("ul").slideUp(function() {
            if (++count == closest_ul.find("ul").length)
                parallel_active_links.removeClass("active");
        });

        if (!link_status) {
            closest_li.children("ul").slideDown();
            closest_li.addClass("active");
        }
    });
    /*end multilevel accordion*/

    $('.search-top-head-mobile').click(function() {
        headerMobile.children().hide();
        headerMobile.append('<input type="text" id="search-input-mobile" name="key" class="form-control" placeholder="search" />' +
            '<i class="fa fa-remove" id="remove-search-input-mobile"></i>' +
            '<i class="fa fa-search" id="search-mobile-fa" attr-action="onSearchValidate" attr-form-id="form-search-mobile"></i>');
    });

    $(document).on('click', '#remove-search-input-mobile', function() {
        $('#search-input-mobile, #remove-search-input-mobile, #search-mobile-fa').remove();
        headerMobile.children().show();
    });

    /**
     * Search
     */
    jQuery.searchData = function(e, submitDiv) {
        e.preventDefault();
        var formId = submitDiv.attr('attr-form-id');
        var action = submitDiv.attr('attr-action');
        var params = {};
        params._token = tokenGenerate;
        params.formData = $.convertFormData($('#'+formId).serializeArray());
        $.callAjax('post', '/'+action, params, $('body'), function(res) {
            if (res.rs == $('#fail-val').val()) {
                $.displayMsg(res.msg[0], 'danger', 1000);
            } else {
                $('#'+formId).submit();
            }
        });
    };

    /*Search in mobile*/
    $(document).on('click', '#search-mobile-fa', function(e) {
        $.searchData(e, $(this));
    });

    /*Search in desktop*/
    $(document).on('click', '#submit-search', function(e) {
        $.searchData(e, $(this));
    });

    //run animate for slick prev or next slide
    $.fn.extend({
        animateCss: function(animationName, callback) {
            var animationEnd = (function(el) {
                var animations = {
                    animation: 'animationend',
                    OAnimation: 'oAnimationEnd',
                    MozAnimation: 'mozAnimationEnd',
                    WebkitAnimation: 'webkitAnimationEnd'
                };

                for (var t in animations) {
                    if (el.style[t] !== undefined) {
                        return animations[t];
                    }
                }
            })(document.createElement('div'));
            this.addClass('animated ' + animationName).one(animationEnd, function() {
                $(this).removeClass('animated ' + animationName);

                if (typeof callback === 'function') callback();
            });
            return this;
        }
    });

    // find all child element, find effect then run effect with each child
    jQuery.animateForSlide = function(divCurrentSlide) {
        divCurrentSlide.find('*').each(function() {//select all child element
            var effect = $(this).attr('data-animation');//find effect
            $(this).animateCss(effect);//animate
        });
    };

    //init slick
    var divHeaderSlider = $('#header-slider');
    divHeaderSlider.on('init', function(event, slick){
        //init slick, put before $(element).slick()
        var divCurrentSlide = $('.slick-slide[data-slick-index="0"]').find('.item-slick');
        $.animateForSlide(divCurrentSlide);
    });
    divHeaderSlider.slick({
        infinite: true,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 1000,
        fade: true,
        prevArrow : '<div class="prev-custom"></div>',
        nextArrow : '<div class="next-custom"></div>',
        responsive: true
    });
    // run animate when prev of next slide
    divHeaderSlider.on('beforeChange', function(e, slick, currentSlide, nextSlide) {//next or prev slide
        var divCurrentSlide = $('.slick-slide[data-slick-index="' + nextSlide + '"]').find('.item-slick');
        $.animateForSlide(divCurrentSlide);
    });

    /*End slick*/

    var tabContent = $('.tab-content');
    tabContent.hide();
    tabContent.eq(0).show();

    /*tab content*/
    $('.nav-tabs-tab').click(function() {
        $('.nav-tabs-tab').removeClass('active');
        $(this).addClass('active');
        var id = $(this).attr('attr-id');
        tabContent.hide().removeClass('active');
        $('#'+id).show().addClass('active');
    });

    /**
     * CART
     */
//    sessionStorage.setItem('cart', '');
    var msgJs = $('#msg_js').text();
    var msgJsObj = JSON.parse(msgJs);
    var tokenGenerate = $('#token_generate').val();
    var currentUrl = window.location.href;

    //count qty in header
    jQuery.countQty = function() {
        var cartRs = sessionStorage.getItem('cart');
        var qty = 0;
        if (cartRs != null && cartRs != '') {
            var cartRsObj = JSON.parse(cartRs);
            $.each(cartRsObj, function(index, value) {
                qty += value.qty
            });
        }
        $('.cart-count').html('<p>'+qty+'</p>');
    };

    //reload cart dropdown in header
    jQuery.reloadCartDropdown = function() {
        var cartRs = sessionStorage.getItem('cart');
        var cartRsObj = {};
        if (cartRs != null && cartRs != '') {
            cartRsObj = JSON.parse(cartRs);
        }
        cartRsObj._token = tokenGenerate;
        $.callAjaxHtml('post', '/onReloadCart', cartRsObj, $('body'), function(response) {
            $('#cart-dropdown').html(response);
        });
    };

    $.countQty();
    $.reloadCartDropdown();

    //add or update cart
    jQuery.addOrUpdateCart = function(thisDiv, qtyAdd, updateQty) {
        var productId = thisDiv.attr('attr-product-id');
        var qtyOrigin = thisDiv.attr('attr-qty');
        var cart = sessionStorage.getItem('cart');
        var cartObject = {};
        if (cart != null && cart != '') {
            cartObject = JSON.parse(cart);
        }
        var itemDetail = {};
        var variantId = thisDiv.attr('attr-variant-id');
        if (variantId != 0) {
            productId = productId+'-'+variantId;
        }
        itemDetail.name = thisDiv.attr('attr-name');
        itemDetail.image = thisDiv.attr('attr-image');
        itemDetail.price = thisDiv.attr('attr-price');
        itemDetail.qty = qtyAdd;//number product customer buy
        itemDetail.qty_origin = qtyOrigin;//quantity in stock
        itemDetail.qty_order = thisDiv.attr('attr-qty-order');//quantity that ordered
        itemDetail.id = productId;
        itemDetail.slug = thisDiv.attr('attr-slug');
        itemDetail.weight = thisDiv.attr('attr-weight');//to calculate ship type weight
        itemDetail.weight_id = thisDiv.attr('attr-weight-id');//to calculate ship type weight
        itemDetail.order_option = $('#order-option-json').text();
        var msg = '';
        if (productId in cartObject) {
            var itemInCart = cartObject[productId];
            itemInCart.qty = itemInCart.qty + 1;
            if (updateQty == true) {
                msg = msgJsObj.update_cart_success;
                itemInCart.qty = qtyAdd;
                $.displayMsg(msg, 'success', 1000);
            } else {
                $('#modalConfirmCart').modal();
            }
            cartObject[productId] = itemInCart;
        } else {
            cartObject[productId] = itemDetail;
            $('#modalConfirmCart').modal();
        }
        sessionStorage.setItem('cart', JSON.stringify(cartObject));
        $.countQty();
        $.reloadCartDropdown();
    };

    //add or update cart in detail page
    jQuery.addOrUpdateInDetail = function(qty, qtyOrder, thisDiv) {
        var qtyInput = $('#quantity').val();
        if (parseInt(qtyInput) + parseInt(qtyOrder) > qty && qty > 0) {//if qty = 0 => not manage stock
            $.displayMsg(msgJsObj.qty_not_enough, 'danger', 1000);
        } else {
            $.addOrUpdateCart(thisDiv, parseInt(qtyInput));
        }
    };

    /**
     * Display currency
     */
    jQuery.displayCurrency = function(price) {
        var constJson = $('#const-json').text();
        var constObj = JSON.parse(constJson);
        var currencyJson = $('#currency-data').text();
        var currencyArray = JSON.parse(currencyJson);
        var symbol = currencyArray.symbol;
        var symbolPosition = currencyArray.symbol_position;
        var symbolPositionBefore = constObj.symbol_position_before;
        var text = '';
        if (symbolPosition == symbolPositionBefore) {
            text = symbol + ' ' + price;
        } else {
            text = price + ' ' + symbol;
        }
        return text;
    };

    /**
     * Get options are chosen
     */
    jQuery.getOptionChosen = function() {
        var SEPARATE = $('#separate-string').val();
        var optionIdChosenArray = [];
        $('.option-choose').each(function() {
            var optionId = $(this).attr('attr-option-id');
            if($(this).is(':checked')) {
                optionIdChosenArray.push(optionId+SEPARATE+$(this).val());
            }
        });
        $('.option-select').each(function() {
            var optionId = $(this).attr('attr-option-id');
            optionIdChosenArray.push(optionId+SEPARATE+$(this).val());
        });
        $('.option-multi-select').each(function() {
            var optionId = $(this).attr('attr-option-id');
            var optionMultiSelectValue = $(this).val();
            if (optionMultiSelectValue.length > 0) {
                $.each(optionMultiSelectValue, function(index, value) {
                    optionIdChosenArray.push(optionId+SEPARATE+value);
                })
            }
        });
        return optionIdChosenArray;
    };

    /**
     * Calculate option price
     */
    jQuery.calculateOptionPrice = function() {
        var optionIdChosenArray = $.getOptionChosen();
        var SEPARATE = $('#separate-string').val();
        var btnBuyNowDetailName = $('#btn-buy-now-detail-name').val();
        var buttonBuyNowDetail = $('#'+btnBuyNowDetailName);
        var FIXEDAMOUNT = $('#fixed_amount').val();
        var valueTypeJson = $('#value-type').text();
        var valueTypeObject = JSON.parse(valueTypeJson);
        var priceAddMore = 0;
        var priceDetailStr = '';
        //price when not add option
        var priceOrigin = buttonBuyNowDetail.attr('attr-origin-price');
        var priceVariant = buttonBuyNowDetail.attr('attr-price-variant');
        var orderOption = [];
        $.each(optionIdChosenArray, function(index, value) {
            var valueArray = value.split(SEPARATE);
            var optionId = valueArray[0];
            var valueId = valueArray[1];
            var valueData = valueTypeObject[optionId][valueId];
            if (valueData !== 'undefined') {
                if (valueData.value_type == FIXEDAMOUNT) {
                    var priceFixed = valueData.value_price;
                    priceAddMore += parseFloat(priceFixed);
                    priceDetailStr += '+ '+valueData.option_name +' '+ $.displayCurrency(parseFloat(priceFixed).toFixed(2));
                } else {
                    var pricePercentage = parseFloat(priceOrigin) * (parseFloat(valueData.value_price) / 100);
                    priceAddMore += parseFloat(pricePercentage);
                    priceDetailStr += '+ '+valueData.option_name +' '+ $.displayCurrency(parseFloat(pricePercentage).toFixed(2));
                }
            }
            //save to #_order_option
            orderOption.push(valueData);
        });
        $('#order-option-json').text(JSON.stringify(orderOption));
        //assign for buy now button
        //toFixed(2) : parse float with two decimal places
        priceVariant = priceVariant != '' ?  priceVariant : 0;
        var finalPrice = parseFloat(priceOrigin) + parseFloat(priceVariant) + parseFloat((priceAddMore.toFixed(2)));
        buttonBuyNowDetail.attr('attr-price', finalPrice.toFixed(2));//for add-to-cart
        $('#product-price-detail-option').html(priceDetailStr);
        $('#product-price-final-price').html($.displayCurrency(finalPrice.toFixed(2)));
    };

    /*Choose option*/
    $('.option-choose').click(function() {
        $.calculateOptionPrice();
    });
    $('.option-select, .option-multi-select').change(function() {
        $.calculateOptionPrice();
    });
    if (document.getElementById('option-div') != null) {
        $.calculateOptionPrice();
    }
    /**
     * End option in detail
     */

    //do buy now
    jQuery.doBuyNow = function(isDetail, thisDiv, qty, qtyOrder) {
        if (isDetail == false) {
            $.addOrUpdateCart(thisDiv, 1);
        } else {
            $.addOrUpdateInDetail(qty, qtyOrder, thisDiv);
        }
    };

    //handle event click to buy product
    jQuery.buyNow = function(thisDiv, isDetail) {
        var qty = thisDiv.attr('attr-qty');
        var qtyOrder = thisDiv.attr('attr-qty-order');
        var productId = thisDiv.attr('attr-product-id');
        var cart = sessionStorage.getItem('cart');
        var cartObject = {};
        if (cart != null && cart != '') {
            cartObject = JSON.parse(cart);
        }
        if (productId in cartObject) { // if product already in cart
            var itemInCart = cartObject[productId];
            var qtyInCart = itemInCart.qty;
            if (parseInt(qtyInCart) + parseInt(qtyOrder) >= qty && qty > 0) {//if qty = 0 => not manage stock
                $.displayMsg(msgJsObj.qty_not_enough, 'danger', 1000);
            } else {
                $.doBuyNow(isDetail, thisDiv, qty, qtyOrder);
            }
        } else {//if product not in cart yet
            $.doBuyNow(isDetail, thisDiv, qty, qtyOrder);
        }
    };

    //buy now in list
    $('.buy-now').click(function() {
        $.buyNow($(this), false);
    });

    //buy now in detail
    $('.buy-now-detail').click(function() {
        $.calculateOptionPrice();
        $.buyNow($(this), true);
    });

    //delete cart item
    jQuery.deleteItem = function(productId) {
        var cartRs = sessionStorage.getItem('cart');
        var cartRsObj = JSON.parse(cartRs);
        delete cartRsObj[productId];
        sessionStorage.setItem('cart', JSON.stringify(cartRsObj));
        $.countQty();
        $.reloadCartDropdown();
    };

    $(document).on('click', '.view-now', function() {
        var slug = $(this).attr('attr-slug');
        var baseUrl = document.location.origin;
        document.location.href = baseUrl + '/' + slug;
    });

    jQuery.ajaxCart = function() {
        var cartRs = sessionStorage.getItem('cart');
        var cartRsObj = JSON.parse(cartRs);
        cartRsObj._token = tokenGenerate;
        $.callAjaxHtml('post', '/onAjaxCart', cartRsObj, $('body'), function(response) {
            $('#cart-div').html(response);
        });
    };

    if (currentUrl.match(/cart/)) {//if page cart
        $.ajaxCart();
    }

    $(document).on('click', '.cart-remove-item-detail', function() {
        var params = {id:$(this).attr('attr-product-id')};
        var msg = msgJsObj.confirm_delete_cart_item;
        $.alertable.confirm(msg, params).then(function() {
            var productId = params.id;
            $.deleteItem(productId);
            $.ajaxCart();
            location.reload();
        });
    });

    jQuery.updateCartItem = function(thisDiv) {
        var productId = thisDiv.attr('attr-product-id');
        var qtyInput = thisDiv.val();
        var qtyOrigin = thisDiv.attr('attr-qty');//quantity in stock
        var qtyOrder = thisDiv.attr('attr-qty-order');//quantity was ordered
        var cartRs = sessionStorage.getItem('cart');
        var cartRsObj = JSON.parse(cartRs);
        var item = cartRsObj[productId];
        var qty = item.qty;//number product customer buy
        if (parseInt(qtyInput) + parseInt(qtyOrder) > parseInt(qtyOrigin) && qtyOrigin > 0) {//if qty = 0 => not manage stock
            $.displayMsg(msgJsObj.qty_not_enough, 'danger', 1000);
        } else {
            $.addOrUpdateCart( thisDiv, parseInt(qtyInput), true);
            $.ajaxCart();
        }
    };

    //catch keypress event
    $(document).on('keypress', '.cart-qty', function(e) {
        if(e.which == 13){
            e.preventDefault();//Enter key pressed
            $.updateCartItem($(this));
        }
    });

    $(document).on('click', '.refresh-qty', function(e) {
        e.preventDefault();//Enter key pressed
        $.updateCartItem($(this).prev());
    });

    /**
     * END CART
     */

    var divModal = $('#divFillModal');

    /**
     * CHECKOUT
     */

    $('.modal-add-user-address').click(function() {
        var params = {};
        params._token = tokenGenerate;
        params.action = currentUrl.replace(baseUrl, '');
        $.callAjaxHtml('post', '/onModalAddress', params, $('#checkout-div'), function(res) {
            divModal.html(res);
            divModal.modal();
        });
    });

    /**
     * END CHECKOUT
     */

    /**
     * Review
     */
     $('#modal-review').click(function() {
         var params = {};
         params._token = tokenGenerate;
         params.productId = $(this).attr('attr-product-id');
         $.callAjaxHtml('post', '/onModalReview', params, $('#checkout-div'), function(res) {
             divModal.html(res);
             $.callAjax('post', '/onCaptcha', params, divModal, function(response) {
                 var folderImage = $('#folder-image').val();
                 var imagePath = baseUrl+folderImage+'captcha/'+response.image;
                 $('#captcha-image').attr('src', imagePath);
                 sessionStorage.setItem('captcha_code', response.code);
             });
             divModal.modal();
         });
     });

    //submit review
    $(document).on('click', '#submit-review', function() {
        var captcha = $('#captcha').val();
        var captchaSession = sessionStorage.getItem('captcha_code');
        if (captcha == captchaSession) {
            var formReview = $.convertFormData($('#form-review-product').serializeArray());
            var review = {};
            review.formReview = formReview;
            review._token = tokenGenerate;
            $.callAjax('post', baseUrl+'/onSubmitReview', review, divModal, function(res) {
                if (res.rs == FAIL) {
                    $.displayMsg(res.msg[0], 'danger', 1000);
                } else {
                    location.reload();
                }
            });
        } else {
            $.displayMsg(msgJsObj.captcha_wrong, 'danger', 1000);
        }
    });

    //pagination review
    $('#reviews').on('click', '.review-pag', function() {
        var params = {};
        params._token = tokenGenerate;
        params.page = $(this).attr('attr-page');
        params.productId = $('#product_id').val();
        $.callAjaxHtml('post', '/onReviewPage', params, $('#checkout-div'), function(res) {
            $('#review-page').html(res);
        });
    });
    /**
     * Review
     */

    /**
     * User
     */
    /*Modal address*/
    $('.edit-user-address').click(function() {
        var id = $(this).attr('attr-id');
        var action = currentUrl.replace(baseUrl, '');
        var params = {id:id, _token: tokenGenerate, action:action};
        $.callAjaxHtml('post', '/onEditAddress', params, $('#user-address-row'), function(res) {
            divModal.html(res);
            divModal.modal();
        });
    });

    $('.delete-user-address').click(function() {
        var id = $(this).attr('attr-id');
        var params = {id:id, _token: $('#token_generate').val()};
        $.alertable.confirm(msgJsObj.delete_address_confirm, id).then(function() {
            $.callAjax('post', '/onDeleteAddress', params, $('#user-address-row'), function(res) {
                if (res.rs == FAIL) {
                    $.displayMsg(res.msg[0], 'danger', 1000);
                } else {
                    location.reload();
                }
            });
        });
    });
    /**
     * User
     */

});



