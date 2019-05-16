/**
 * CATEGORY
 */
function addOrUpdateQueryParam(search, param, newval) {
    var questionIndex = search.indexOf('?');
    if (questionIndex < 0) {
        search = search + '?';
        search = search + param + '=' + newval;
        return search;
    }
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');
    var indexOfEquals = query.indexOf('=');
    return (indexOfEquals >= 0 ? query + '&' : query + '') + (newval ? param + '=' + newval : '');
}

//remove params
function removeParamsFromUrl(param, url) {
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    return url.replace(regex, "$1").replace(/&$/, '');
}

//get url param
function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}

function getAllUrlParams(url) {
    // get query string from url (optional) or window
    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
    // we'll store the parameters here
    var obj = {};
    // if query string exists
    if (queryString) {
        // stuff after # is not part of query string, so get rid of it
        queryString = queryString.split('#')[0];
        // split our query string into its component parts
        var arr = queryString.split('&');
        for (var i=0; i<arr.length; i++) {
            // separate the keys and the values
            var a = arr[i].split('=');
            // in case params look like: list[]=thing1&list[]=thing2
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function(v) {
                paramNum = v.slice(1,-1);
                return '';
            });
            // set parameter value (use 'true' if empty)
            var paramValue = typeof(a[1])==='undefined' ? true : a[1];
            // (optional) keep case consistent
            paramName = paramName.toLowerCase();
            paramValue = paramValue.toLowerCase();

            // if parameter name already exists
            if (obj[paramName]) {
                // convert value to array (if still string)
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                // if no array index number specified...
                if (typeof paramNum === 'undefined') {
                    // put the value on the end of the array
                    obj[paramName].push(paramValue);
                }
                // if array index number specified...
                else {
                    // put the value at that index number
                    obj[paramName][paramNum] = paramValue;
                }
            }
            // if param name doesn't exist yet, set it
            else {
                obj[paramName] = paramValue;
            }
        }
    }
    return obj;
}

$(document).ready(function() {
    var constJson = $('#const_json').text();
    var constObject = JSON.parse(constJson);
    var currentUrl = window.location.href;
    var ENABLE = constObject.enable;
    var nowShoppingByDiv = 'now-shopping-by-div';
    var isDisplayPriceSlider = $('#is_display_price_slider').val();
    var tokenGenerate = $('#token_generate').val();

    /*Price range in category*/
    $.priceSlider = function() {
        $("#js-range-slider").ionRangeSlider({
            type: 'double',
            onFinish: function(obj) {
                var from = obj.from;
                var to = obj.to;
                $.loadData('price_range', from+'-'+to);
            }
        });
    };

    /*initial price slider*/
    $.priceSlider();

    /*Menu accordion*/
    jQuery.filterAccordion = function() {
        $('.accordion').click(function() {
            if ($(this).hasClass('open')) {
                $(this).next().slideUp();
                $(this).removeClass('open');
            } else {
                $(this).next().slideDown();
                $(this).addClass('open');
            }
        });
    };
    $.filterAccordion();

    //initial params for category if first time visit
    sessionStorage.setItem('params', '');

    //initial price slider
    if (isDisplayPriceSlider == ENABLE) {
        $.priceSlider();
    }

    //Now Shopping by
    jQuery.nowShoppingBy = function(params) {
        params.filter_data = $('#filter_data_json').text();
        params._token = tokenGenerate;
        $.callAjaxHtml('post', '/onNowShopBy', params, $('.row-product-list'), function(res) {
            $('#now-shopping-by').html(res)
        });
        //hide or show now-shopping-by-div base on if filter exists
        if (params.hasOwnProperty('price_range') || params.hasOwnProperty('reviews') ||
            params.hasOwnProperty('key')  || params.hasOwnProperty('filter') ||
            params.hasOwnProperty('brand')) {
            if (params.filter != '') {//check when check on/off filter-option
                $('#'+nowShoppingByDiv).show();
            } else {
                $('#'+nowShoppingByDiv).hide();
            }
        } else {
            $('#'+nowShoppingByDiv).hide();
        }
    };

    //initial now shopping by
    $.nowShoppingBy(getAllUrlParams(currentUrl));

    $(document).on('click', '#filter-modal-open', function() {//mobile
        $('#filter-div-modal').modal();
        $('.panel').slideUp().addClass('active');
        $('.accordion').find('.fa-filter-accordion').addClass('fa-plus');//for mobile
    });

    /**
     * Load category ajax
     */
    jQuery.loadCategoryAjax = function(params) {
        params.id = $('#category_id').val();
        params._token = tokenGenerate;
        $.callAjaxHtml('post', '/onCategoryAjax', params, $('.row-product-list'), function(res) {
            $('#category-div').html(res);
            $.filterAccordion();
            if (isDisplayPriceSlider == ENABLE) {
                $.priceSlider();
            }
            $.nowShoppingBy(params);
        });

    };

    /**
     * ajax load Data keep params in url
     */
    jQuery.loadDataKeepParamsInUrl = function(paramsAssign, value) {
        var url = '';
        if (paramsAssign == 'filter' && value == []) {
            //var re = /([?;&])filter=\d/;//replace with regular expression
            var re = /filter=\d/;
            currentUrl = currentUrl.replace(re, 'filter=');//remove filter string if there is not filter
            url = currentUrl;
        } else {
            if (paramsAssign != 'page' && paramsAssign != 'sort_by') {
                //return to page 1 if params assign not 'page' and 'sort_by' => pagination
                currentUrl = addOrUpdateQueryParam(currentUrl, 'page', 1);
            }
            url = addOrUpdateQueryParam(currentUrl, paramsAssign, value);
        }
        var params = getAllUrlParams(url);
        params[paramsAssign] = value;//override page
        history.pushState($.loadCategoryAjax(params), null, url);
    };


    //load data based on method : normal or ajax
    jQuery.loadData = function(paramsAssign, value) {
        currentUrl = window.location.href;//reload current url to get full params in get
        $.loadDataKeepParamsInUrl(paramsAssign, value);
        $(window).scrollTop($('#category-div').offset().top);
    };

    //HANDLE FILTER - PROPERTY
    //handle filter
    jQuery.handleFilterToLoadData = function() {
        var propertyIdArray = [];
        $('.property-filter-checkbox').each(function() {
            if ($(this).is(':checked')) {
                propertyIdArray.push($(this).val());
            }
        });
        $('.property_color').each(function() {
            if ($(this).hasClass('checked')) {
                propertyIdArray.push($(this).attr('property-id'));
            }
        });
        var strFilterOptionId = propertyIdArray.join('_');
        $.loadData('filter', strFilterOptionId);
    };

    //click to li.property-filter
    $(document).on('click', '.property-filter', function() {
        var propertyIdCheck = $(this).attr('property-id');
        if ($('#property-filter-checkbox-'+propertyIdCheck).is(':checked')) {//off
            document.getElementById('property-filter-checkbox-'+propertyIdCheck).checked = false;
        } else {//on
            document.getElementById('property-filter-checkbox-'+propertyIdCheck).checked = true;
        }
        $.handleFilterToLoadData();
    });

    //click to checkbox .filter-option-checkbox
    $(document).on('click', '.property-filter-checkbox', function(e) {
        e.stopPropagation();//stop click event on parent - click to .filter-option
        $.handleFilterToLoadData();
    });

    //click to .property_color
    $(document).on('click', '.property_color', function() {
        $(this).addClass('checked');
        $.handleFilterToLoadData();
    });
    //HANDLE FILTER - PROPERTY

    //sort by
    $(document).on('change', '#product-sort-by', function() {
        var sortBy = $(this).val();
        $.loadData('sort_by', sortBy);
    });

    //pagination
    $(document).on('click', '.cat-pag', function() {
        var page = $(this).attr('attr-page');
        $.loadData('page', page);
    });

    jQuery.searchProduct = function() {
        var searchKey = $('#search_product').val();
        if (searchKey != '') {
            $.loadData('key', searchKey);
        }
    };

    //filter by review
    $(document).on('click', '.review-point', function() {
        var reviewPoint = $(this).attr('attr-point');
        $.loadData('reviews', reviewPoint);
    });

    //filter by brand
    $(document).on('click', '.brand-image', function() {
        var id = $(this).attr('attr-brand-id');
        $.loadData('brand', id);
    });

    //Search
    $(document).on('click', '#category-fa-search', function() {
        $.searchProduct();
    });
    //catch keypress event - enter when search product
    $(document).on('keypress', '#search_product', function(e) {
        if(e.which == 13){
            e.preventDefault();//Enter key pressed
            $.searchProduct();
        }
    });

    //for back button when use push state
    $(window).on("popstate", function (e) {
        location.reload();
    });

    //handle filter that to be removed
    jQuery.handleFilterRemove = function(propertyIdRemove) {
        var propertyIdInUrl = getURLParameter('filter');
        var propertyInUrlArray = propertyIdInUrl.split('_');
        if (propertyInUrlArray.length > 1) {//remove id from array if array.length > 1
            var index = propertyInUrlArray.indexOf(propertyIdRemove);
            if (index !== -1) propertyInUrlArray.splice(index, 1);
            var propertyString = '';
            propertyString += propertyInUrlArray[0];
            if (propertyInUrlArray.length > 1) {
                for (var i=1; i<propertyInUrlArray.length; i++) {
                    propertyString += '_' + propertyInUrlArray[i];
                }
            }
            return propertyString;
        }
        return '';
    };

    //handle remove now shop by item when keep params in url
    jQuery.removeNowShopByItem = function(paramRemove, removeItemDiv) {
        var url = '';
        var currentUrl = window.location.href;
        //handle remove 'filter'
        if (paramRemove == 'filter') {
            var propertyIdRemove = removeItemDiv.attr('attr-id');
            var propertyString = $.handleFilterRemove(propertyIdRemove);
            if (propertyString != '') {
                url = addOrUpdateQueryParam(currentUrl, 'filter', propertyString);
                var params = getAllUrlParams(url);
                history.pushState($.loadCategoryAjax(params), null, url);
                return;
            }
        }
        url = removeParamsFromUrl(paramRemove, currentUrl);
        history.pushState($.loadCategoryAjax(getAllUrlParams(url)), null, url);
    };

    //Remove now shop by item
    $(document).on('click', '.remove-shop-by-item', function() {
        var paramRemove = $(this).attr('attr-remove');
        $.removeNowShopByItem(paramRemove, $(this));
    });

    //clear all now shop by
    $(document).on('click', '#clear-all', function() {
        var currentUrl = window.location.href;
        var url = currentUrl.substring(0,currentUrl.indexOf("?"));
        history.pushState($.loadCategoryAjax({}), null, url);
    });


});
/**
 * END CATEGORY
 */