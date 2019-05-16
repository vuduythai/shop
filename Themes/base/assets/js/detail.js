/**
 * PRODUCT DETAIL
 */
function xZoomGallery() {
    /*xzoom in product detail*/
    $(".xzoom").xzoom({tint: '#333', Xoffset: 15});
    $(".xzoom-thumbs").slick({
        slidesToShow: 4,
        prevArrow : '<div class="prev-thumb"></div>',
        nextArrow : '<div class="next-thumb"></div>'
    });
}

$(document).ready(function() {
    var SEPARATE = $('#separate-string').val();
    var btnBuyNowDetailName = $('#btn-buy-now-detail-name').val();
    var buttonBuyNowDetail = $('#'+btnBuyNowDetailName);
    var FIXEDAMOUNT = $('#fixed_amount').val();
    var baseUrl = window.location.origin;
    var tokenGenerate = $('#token_generate').val();
    var isChangeVariantImage = $('#is_variant_change_image').val();
    var YES = $('#yes_const').val();
    var msgJs = $('#msg_js').text();
    var msgJsObj = JSON.parse(msgJs);
    var qtyDetailDiv = $('#qty-detail-div');
    var priceDetailVariant = $('#product-price-detail-variant');

    xZoomGallery();

    //handle last option: change price, gallery
    jQuery.handleLastProperty = function() {
        var activeProperty = [];
        $('.property-detail').each(function() {
            if ($(this).hasClass('active')) {
                activeProperty.push($(this).attr('property-id'));
            }
        });
        var activePropertyString = activeProperty.join(SEPARATE);
        var variantData = $('#variant-data').text();
        var variantObject = JSON.parse(variantData);
        var variant = variantObject[activePropertyString];
        //reload gallery
        if (isChangeVariantImage == YES) {
            variant._token = tokenGenerate;
            $.callAjaxHtml('post', baseUrl+'/onReloadGallery', variant, $('#product-gallery'), function(response) {
                $('#product-gallery').html(response);
                xZoomGallery();
            });
        }
        //end reload gallery
        qtyDetailDiv.removeClass('class-hidden');
        buttonBuyNowDetail.attr('attr-qty', variant.qty_variant);
        buttonBuyNowDetail.attr('attr-qty-order', variant.qty_order);
        if (isChangeVariantImage == YES) {
            var variantImage = variant.variant_image != null ? variant.variant_image : '';
            buttonBuyNowDetail.attr('attr-image', variantImage);
        }
        var originPrice = buttonBuyNowDetail.attr('attr-origin-price');
        buttonBuyNowDetail.attr('attr-price', parseFloat(originPrice) + parseFloat(variant.price_variant));
        buttonBuyNowDetail.attr('attr-price-variant', variant.price_variant);
        buttonBuyNowDetail.attr('attr-variant-id', variant.id);
        var variantPrice = $.displayCurrency(variant.price_variant);
        priceDetailVariant.html('+ '+msgJsObj.variant_text+' '+variantPrice);
        priceDetailVariant.show();
        $.calculateOptionPrice();//template.js
    };

    //display option next level
    jQuery.displayOptionNextLevel = function(propertyNext, currentLevel) {
        var arrayPropertyNext = propertyNext.split(SEPARATE);
        var nextLevel = parseInt(currentLevel) + 1;
        $('.level-'+ nextLevel).hide();//hide all next level property
        $('.name-level-'+nextLevel).show();//show next level attribute name
        $.each(arrayPropertyNext, function(index, value) {
            //just show next property of active current level property
            $('#property-'+value).show();
        })
    };

    //remove active of next level
    jQuery.removeActiveOfNextLevel = function(currentLevel) {
        var allLevel = [];
        $('.property-detail').each(function() {
            allLevel.push($(this).attr('attr-level'));
        });
        var maxLevel = Math.max.apply(Math, allLevel);
        for (var i = currentLevel; i<=maxLevel; i++) {
            $('.level-'+i).removeClass('active');
        }
        return maxLevel;
    };

    //hide next level property and name of next level attribute
    jQuery.hideNextLevel = function(currentLevel, maxLevel) {
        if (currentLevel < maxLevel) {
            if (maxLevel - currentLevel == 1) {
                $('.level-'+maxLevel).hide();
                $('.name-level-'+maxLevel).hide();
            } else {
                for (var y = currentLevel + 1; y<=maxLevel; y++) {
                    $('.level-'+y).hide();
                    $('.name-level-'+y).hide();
                }
            }
        }
    };

    //handle event click to .property-detail
    jQuery.handlePropertyClick = function(thisProperty) {
        var propertyNext = thisProperty.attr('attr-next');
        var currentLevel = thisProperty.attr('attr-level');
        $('.level-'+currentLevel).removeClass('active');
        var maxLevel = $.removeActiveOfNextLevel(currentLevel);
        thisProperty.addClass('active');
        if (propertyNext != '') {//NOT last option
            qtyDetailDiv.addClass('class-hidden');
            priceDetailVariant.hide();
            $.displayOptionNextLevel(propertyNext, currentLevel);
        } else {//last option
            $.hideNextLevel(currentLevel, maxLevel);
            $.handleLastProperty();
        }
    };

    var productType = $('#product-type').val();
    var productTypeConfig = $('#product-type-config').val();
    if (productType == productTypeConfig) {
        var propertyDetail = $('.property-detail');
        //initial first property
        $.handlePropertyClick(propertyDetail.eq(0));

        //click to property
        propertyDetail.click(function() {
            $.handlePropertyClick($(this));
        });
    }

    var quantityDiv = $('#quantity');
    //add product qty
    $('.add-product-qty').click(function() {
        var qty = quantityDiv.val();
        quantityDiv.val(parseInt(qty) + 1);
    });

    $('.subtract-product-qty').click(function() {
        var qty = quantityDiv.val();
        if (qty > 1) {
            quantityDiv.val(parseInt(qty) - 1);
        }
    });
});

/**
 * PRODUCT DETAIL
 */