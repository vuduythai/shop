var folderImage = document.getElementById('folder-image').value;

function globalObject() {
    var obj = {};
    obj.folderImage = folderImage;
    obj.fail = document.getElementById('result-fail').value;
    obj.success = document.getElementById('result-success').value;
    obj.action_create = document.getElementById('action-create').value;
    obj.action_update = document.getElementById('action-update').value;
    obj.waitMeColor = document.getElementById('wait-me-color').value;
    if (document.getElementById('msg_js') != null) {
        var msgJsJson = document.getElementById('msg_js').innerText;
        obj.msgJs = JSON.parse(msgJsJson);
    }
    return obj;
}

/**
 * Get params in url string
 */
function getParamsInUrlString(url)
{
    var vars = [], hash;
    //var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    var hashes = url.slice(url.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function array_diff(array1, array2)
{
    return array1.filter(function(elm) {
        return array2.indexOf(elm) === -1;
    })
}

$(document).ready(function() {
    var gb = globalObject();//have to be different name to 'globalObject' in one file
    var waitMeColor = gb.waitMeColor;
    var baseUrl = window.location.origin;
    var currentUrl = window.location.href;
    var adminUrl = $('#admin_url').val();
    var currentUri = currentUrl.replace(adminUrl, "");
    var msgJs = gb.msgJs;
    var divModal = $('#divFillModal');
    var tokenGenerate = $('#token_generate').text();

    /**
     * HELPERS
     */
    jQuery.fn.serializeJSON=function() {
        var json = {};
        jQuery.map(jQuery(this).serializeArray(), function(n, i) {
            var _ = n.name.indexOf('[');
            if (_ > -1) {
                var o = json;
                _name = n.name.replace(/\]/gi, '').split('[');
                for (var i=0, len=_name.length; i<len; i++) {
                    if (i == len-1) {
                        if (o[_name[i]]) {
                            if (typeof o[_name[i]] == 'string') {
                                o[_name[i]] = [o[_name[i]]];
                            }
                            o[_name[i]].push(n.value);
                        }
                        else o[_name[i]] = n.value || '';
                    }
                    else o = o[_name[i]] = o[_name[i]] || {};
                }
            }
            else {
                if (json[n.name] !== undefined) {
                    if (!json[n.name].push) {
                        json[n.name] = [json[n.name]];
                    }
                    json[n.name].push(n.value || '');
                }
                else json[n.name] = n.value || '';
            }
        });
        return json;
    };

    $('.select2').select2();

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

    jQuery.initTinyMce = function(divId) {
        tinymce.init({
            selector: '#'+divId,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            document_base_url: window.location.origin,
            height: 500,
            code_dialog_height:600,
            code_dialog_width:1200,
            theme: 'modern',
            skin: "lightgray_gradient",
            plugins: 'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help code',
            toolbar1: 'formatselect | fontsizeselect | bold italic strikethrough forecolor backcolor | image link unlink | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat code fullscreen',
            file_browser_callback: function(field, url, type, win) {
                tinyMCE.activeEditor.windowManager.open({
                    file: '/kcfinder/browse.php?opener=tinymce4&field=' + field + '&type=' + type,
                    title: 'KCFinder',
                    width: 1200,
                    height: 600,
                    inline: true,
                    close_previous: false
                }, {
                    window: win,
                    input: field
                });
                return false;
            }
        });
    };

    /**
     * Add an image by kcfinder
     */
    jQuery.openKCFinder = function(field, fieldAppendValue, type) {
        window.KCFinder = {
            callBack: function(url) {
                var image = url.replace(folderImage, '');
                //field.value = url;//javascript set field value
                $('#'+fieldAppendValue).val(image);//assign to hidden field
                var divAppend = '<img src="'+baseUrl + folderImage + image +'" class="image" id="media-'+fieldAppendValue+'"/>'+
                    '<div class="overlay">'+
                    '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>'+
                    '</div>';
                type || (type = 'image');//default is image
                if (type == 'image') {
                    field.html(divAppend);
                } else {//if type == 'text'
                    field.val(image);
                }
                window.KCFinder = null;
            }
        };
        window.open(''+baseUrl+'/kcfinder/browse.php?type=image',
            'kcfinder_multiple', 'status=0, toolbar=0, location=0, menubar=0, ' +
                'directories=0, resizable=1, scrollbars=0, width=800, height=600'
        );
    };

    /**
     * Create gallery - kcfinder add many images
     * field: field that append many images - gallery
     */
    jQuery.createGallery = function(field, imageInnerClass) {
        window.KCFinder = {
            callBackMultiple: function(files) {
                //console.log(files);
                window.KCFinder = null;
                $.each(files, function(index, value) {
                    //console.log(value);
                    var image = value.replace(folderImage, '');
                    var imageDiv = '<div class="image-gallery-outer">'+
                        '<img class="img-delete" src="'+baseUrl+'/modules/backend/assets/img/x.png'+'" /> ' +
                        '<input name="'+field+'[]" value="'+image+'" type="hidden">'+
                        '<img class="image-gallery" src="'+baseUrl + folderImage + image+'"/>' +
                        '</div>';
                    $('#'+field).prepend(imageDiv);
                });
            }
        };
        window.open(''+baseUrl+'/kcfinder/browse.php?type=image',
            'kcfinder_multiple', 'status=0, toolbar=0, location=0, menubar=0, ' +
                'directories=0, resizable=1, scrollbars=0, width=800, height=600'
        );
    };

    $(document).on('click', '.img-delete', function() {//remove image
        $(this).parent().remove();
    });

    jQuery.generateString = function(num) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789";
        for (var i = 0; i < num; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    };

    /**
     * Toggle all
     */
    jQuery.toggleAllCheckBox = function(source, className) {
        var checkboxes = document.getElementsByClassName(className);
        for (var i=0, n=checkboxes.length; i<n; i++) {
            checkboxes[i].checked = source.checked;
        }
    };

    jQuery.select2Autocomplete = function(selectDiv, url) {
        $(selectDiv).select2({
            ajax: {
                url: url,
                method: "post",
                dataType: 'json',
                delay: 0,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        _token: $('input[name="_token"]').val()
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data, function(obj) {
                            return { id: obj.id, text: obj.text};
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 2
        });
    };
    /**
     * END HELPERS
     */

    /**
     * BACKEND
     */
    var SEPARATE_STRING = $('#separate-string').val();
    var CLOSE = 1;
    var NOT_CLOSE = 2;

    //validate form dynamic create and update
    jQuery.validateThenSave = function(e, action, divWaitMe, close) {
        $(document).trigger('beforeClickButtonSaveData');//create event beforeClickButtonSaveData
        e.preventDefault();
        var params = {};
        params.controller = $('#controller-name').val();
        params._token = tokenGenerate;
        var formDynamic = $('.form_dynamic');
        params.formData = formDynamic.serializeJSON();
        close || (close = NOT_CLOSE);//default is NOT_CLOSE
        params.closeRs = close;
        divWaitMe || (divWaitMe = $('.form-box-content'));//default is $('.form-box-content')
        $.callAjax('post', action, params, divWaitMe, function(res) {
            if (res.rs == gb.fail) {
                $.displayMsg(res.msg[0], 'error', 1000);
            } else {
                if (res.closeRs == CLOSE) {
                    window.location.href = adminUrl + '/' +params.controller
                } else {
                    window.location.href = adminUrl + '/' +params.controller + '/' + res.id + '/edit';
                }
            }
        });
    };

    $('.btn_save').click(function(e){
        var controller = $(this).attr('attr-controller');
        $.validateThenSave(e, adminUrl+'/'+controller, $('.form-box-content'), NOT_CLOSE);
    });

    $('.btn_save_and_close').click(function(e){
        var controller = $(this).attr('attr-controller');
        $.validateThenSave(e, adminUrl+'/'+controller, $('.form-box-content'), CLOSE);
    });

    $('#invoice-template-submit').click(function(e){
        $.validateThenSave(e, adminUrl+'/invoice-save-template', $('.form-box-content'), CLOSE);
    });

    $('#btn-currency-convert').click(function(e){
        $.validateThenSave(e, adminUrl+'/currency-convert', $('.list-box'), CLOSE);
    });

    $('#btn-send-mail').click(function(e) {
        var adminUrl = $('#admin_url').val();
        $.validateThenSave(e, adminUrl+'/customer/validate-then-send-mail');
    });

    //display message when success redirect to index
    if (document.getElementById('msg_display') != null) {
        var divMsgDisplay = $('#msg_display');
        var msg = divMsgDisplay.text();
        $.displayMsg(msg, 'success', 1000);
    }

    //string to slug with field #name
    $('#name').stringToSlug({
        getPut: '#slug'
    });

    /**
     * GROUP LIST
     */
    $('.search-filter').keyup(function() {
        var str = $(this).val();
        if (str.length > 1) {
            $('#form-filter-search').submit();
        }
    });
    $('.select-filter').change(function() {
        $('#form-filter-search').submit();
    });

    //Config
    if (currentUri.match(/setting/)) {
        $('.todo-list li').click(function() {
            window.location.href = $(this).find('a').attr('href');
        });
    }

    $('.base-list tbody tr td').not('.checkbox-td-list').click(function() {
        window.location.href = $(this).parent().attr('attr-url-edit');
    });

    var buttonDelete  = $('#button-delete');
    var checkBoxRecordDiv = $('.checkbox-record');
    var checkAllDiv = $('#check-all-record');

    checkBoxRecordDiv.click(function() {
        var strArray = buttonDelete.attr('attr-id');
        var arrayId = strArray != 0 ? strArray.split(SEPARATE_STRING) : [];
        var id = $(this).val();
        if ($(this).is(':checked')) {
            arrayId.push(id);
        } else {
            var index = arrayId.indexOf(id);
            if (index > -1) {
                arrayId.splice(index, 1);
            }
        }
        if (arrayId.length >= 1) {
            buttonDelete.addClass('btn-warning can-delete');
        } else {
            buttonDelete.removeClass('btn-warning can-delete');
        }
        buttonDelete.attr('attr-id', arrayId.join(SEPARATE_STRING));
    });

    //check all
    checkAllDiv.click(function() {
        var checkBoxArray = [];
        if($(this).is(':checked')) {
            buttonDelete.addClass('btn-warning can-delete');
            checkBoxRecordDiv.each(function() {
                checkBoxArray.push($(this).val());
            });
            var strId = checkBoxArray.join(SEPARATE_STRING);
            buttonDelete.attr('attr-id', strId)
        } else {
            buttonDelete.removeClass('btn-warning can-delete');
            buttonDelete.attr('attr-id', '')
        }
    });

    $(document).on('click', '.can-delete', function() {
        var strId = $(this).attr('attr-id');
        var controller = $('#controller-name').val();
        var params = {};
        params.str_id = strId;
        params.controller = controller;
        params._token = tokenGenerate;
        $.alertable.confirm(msgJs.delete_record, params).then(function() {
            // do something with params
            $.ajax({
                url: adminUrl +'/'+params.controller+'/'+params.str_id,
                type: 'DELETE',
                data: {_token:params._token},
                beforeSend: function() {
                    $('.list-box-content').waitMe({color: waitMeColor});
                },
                success: function(res) {
                    $('.list-box-content').waitMe('hide');
                    if (res.rs == gb.fail) {
                        $.displayMsg(res.msg[0], 'error', 1000);
                    } else {
                        window.location.href = adminUrl + '/' +params.controller
                    }
                }
            });
        });
    });

    /*Copy product*/
    jQuery.copyProduct = function(productId) {
        var params = {};
        params._token = tokenGenerate;
        params.productId = productId;
        $.callAjax('post', adminUrl+'/product/onCopyProduct', params, $('.form-box-content'), function(res) {
            if (res.rs == gb.fail) {
                $.displayMsg(res.msg[0], 'error', 1000);
            } else {
                window.location.href = adminUrl + '/product';
            }
        });
    };

    //copy product
    $('#button-copy-product').click(function() {
        var arrayId = [];
        checkBoxRecordDiv.each(function() {
           if ($(this).is(':checked')) {
               arrayId.push($(this).val());
           }
        });
        if (arrayId.length == 1) {
            $.copyProduct(arrayId[0]);
        } else if (arrayId.length == 0) {
            $.displayMsg(msgJs.at_least_one, 'error', 1000);
        } else {
            $.displayMsg(msgJs.just_choose_one, 'error', 1000);
        }
    });
    /**
     * END GROUP LIST
     */

    /**
     * ATTRIBUTE
     */
    if (currentUrl.match(/attribute/) && currentUrl.match(/create/) && !currentUri.match(/attribute_set/) && !currentUri.match(/attribute_group/) ||
        currentUrl.match(/attribute/) && currentUrl.match(/edit/) && !currentUri.match(/attribute_set/) && !currentUri.match(/attribute_group/)) {
        var TYPE_TEXT = $('#type-text').val();
        var TYPE_COLOR = $('#type-color').val();

        //display option by value type
        jQuery.displayPropertyValueByType = function(type) {
            var valueDiv = $('.property_value');
            if (type == TYPE_TEXT) {
                valueDiv.hide().attr('name', '');
                $('#property-value-text').show().attr('name', 'value');
            }
            if (type == TYPE_COLOR) {
                valueDiv.hide().attr('name', '');
                $('#property-value-color').show().attr('name', 'value');
            }
        };
        //use slug and kcfinder to dynamic div
        jQuery.useSlug = function() {
            $('#property-name').stringToSlug({
                getPut: '#property-slug'
            });
        };
        //event onCreateItemModal is defined in many.js
        $(document).on('onCreateItemModal', function() {
            var typeSelectDiv = $('#type');
            $.useSlug();
            var type = typeSelectDiv.val();
            $.displayPropertyValueByType(type);
            $('#property-type').val(typeSelectDiv.val());
        });
        //event onUpdateItemModal is defined in many.js
        $(document).on('onUpdateItemModal', function() {
            var type = $('#property-type').val();
            $.displayPropertyValueByType(type);
            $.useSlug();
        });
    }
    /**
     * ATTRIBUTE
     */

    /**
     * ATTRIBUTE SET
     */
    if (currentUrl.match(/attribute_set/) && currentUrl.match(/create/) ||
        currentUrl.match(/attribute_set/) && currentUrl.match(/edit/)) {
        var attributeDefaultList = document.getElementById('attribute-default-list');
        var attributeSetList = document.getElementById('attribute-set-list');
        new Sortable(attributeDefaultList, {
            group: 'shared', // set both lists to same group
            animation: 150,
            onEnd: function (/**Event*/evt) {
                $('#attribute-set-list').find('.list-group-item').each(function() {
                    $(this).find('.drag_attr_id').attr('name', 'drag_attr_id[]');
                });
            }
        });
        new Sortable(attributeSetList, {
            group: 'shared',
            animation: 150,
            onEnd: function(evt) {
                $('#attribute-default-list').find('.list-group-item').each(function() {
                    $(this).find('.drag_attr_id').attr('name', '');
                });
            }
        });
    }
    /**
     * ATTRIBUTE SET
     */

    /**
     * SHIPPING
     */
    jQuery.showShippingType = function(arrayIdHide, arrayIdShow) {
        for (var i=0; i<arrayIdHide.length; i++) {
            $('#'+arrayIdHide[i]).parent().hide();
        }
        for (var y=0; y<arrayIdShow.length; y++) {
            $('#'+arrayIdShow[y]).parent().show();
        }
    };
    jQuery.displayShippingTypeField = function(type) {
        var TYPE_PRICE = 1;
        var TYPE_GEO = 2;
        var TYPE_WEIGHT_BASED = 3;
        var TYPE_PER_ITEM = 4;
        var TYPE_GEO_WEIGHT_BASED = 5;
        if (type == TYPE_PRICE) {
            $.showShippingType(['weight_based', 'geo_zone_id', 'weight_type'], ['above_price', 'cost']);
        }
        if (type == TYPE_GEO) {
            $.showShippingType(['above_price', 'weight_based', 'weight_type'], ['geo_zone_id', 'cost']);
        }
        if (type == TYPE_WEIGHT_BASED) {
            $.showShippingType(['above_price', 'geo_zone_id', 'cost'], ['weight_based', 'weight_type']);
        }
        if (type == TYPE_PER_ITEM) {
            $.showShippingType(['above_price', 'geo_zone_id', 'weight_based', 'weight_type'], ['cost']);
        }
        if (type == TYPE_GEO_WEIGHT_BASED) {
            $.showShippingType(['above_price', 'cost'], ['geo_zone_id', 'weight_based', 'weight_type']);
        }
    };
    //shipping edit
    if (currentUri.match(/shipping/) && currentUri.match(/create/) ||
        currentUri.match(/shipping/) && currentUri.match(/edit/)) {
        var typeDiv = $('#type');
        var type = typeDiv.val();
        $.displayShippingTypeField(type);
        typeDiv.change(function(){
            var type = $(this).val();
            $.displayShippingTypeField(type);
        });
    }
    /**
     * END SHIPPING
     */

    /**
     * COUPON
     */

    if (currentUrl.match(/coupon/) && currentUrl.match(/create/) ||
        currentUrl.match(/coupon/) && currentUrl.match(/edit/) ) {
        var startDate = new Pikaday({
            field: document.getElementById('start_date'),
            format: 'YYYY-MM-DD',
            minDate: new Date()
        });
        var endDate = new Pikaday({
            field: document.getElementById('end_date'),
            format: 'YYYY-MM-DD',
            minDate: new Date()
        });

        var categorySearchUrl = adminUrl+"/coupon/onSearchCategory";
        var productSearchUrl = adminUrl+"/coupon/onSearchProduct";
        $.select2Autocomplete('#category-search', categorySearchUrl);
        $.select2Autocomplete('#product-search', productSearchUrl);

        //add refresh coupon code
        $(document).on('click', '#refresh-coupon', function() {
            var couponPrefix = $('#coupon_prefix').val();
            var couponLengthRandom = $('#coupon_length_random').val();
            var randomString = $.generateString(couponLengthRandom);
            var randomCode = couponPrefix + randomString;
            $('#code-coupon-admin').val(randomCode);
        });
    }
    //assign product and category coupon when update coupon
    if (currentUrl.match(/coupon/) && currentUrl.match(/edit/)) {
        var categoryStr = $('#category_update').val();
        var categoryArray = categoryStr.split(',');
        var productStr = $('#product_update').val();
        var productArray = productStr.split(',');
        $('#category-search').val(categoryArray).trigger('change');
        $('#product-search').val(productArray).trigger('change');
    }
    /**
     * END COUPON
     */

    /**
     * PRODUCT
     */
    if (currentUrl.match(/product/) && currentUrl.match(/create/) ||
        currentUrl.match(/product/) && currentUrl.match(/edit/) ) {

        $('#category\\[\\], #category_default').select2({
            templateSelection: function (result) {
                var text = result.text;
                var newText = text.replace(/¦-- /g, '');
                return newText;
            }
        });

        var attributeSetIdDiv = $('#attribute_set_id');

        var from = new Pikaday({
            field: document.getElementById('price_promo_from'),
            format: 'YYYY-MM-DD',
            minDate: new Date()
        });
        var to = new Pikaday({
            field: document.getElementById('price_promo_to'),
            format: 'YYYY-MM-DD',
            minDate: new Date()
        });

        $(document).on('beforeClickButtonSaveData', function() {
            //add sort value for option value
            $('.tbody-option-value').each(function() {
               var i = 1;
               $(this).find('.option_value_sort').each(function() {
                   $(this).val(i);
                   i++;
               })
            });
            //add sort for attribute
            var y = 1;
            $('.attribute_sort_order').each(function() {
                $(this).val(y);
                y++;
            });
            attributeSetIdDiv.attr('disabled', false);
        });

        if (currentUrl.match(/product/) && currentUrl.match(/edit/)) {
            var productAttributeTbody = document.getElementById('product-attribute-tbody');
            Sortable.create(productAttributeTbody);
        }

        //CKEDITOR.replace('full_intro');
        $.initTinyMce('full_intro');


        /*get chosen data for multiselect field*/
        jQuery.getMultiSelectChosen = function(divGetChosenValue) {
            var chosen = $('#'+divGetChosenValue).val();
            return chosen.split(SEPARATE_STRING);
        };

        /*Display attribute by attribute group id*/
        if (currentUrl.match(/product/) && currentUrl.match(/create/)) {
            jQuery.displayAttribute = function(attributeSetId) {
                var params = {};
                params._token = tokenGenerate;
                params.attributeSetId = attributeSetId;
                params.id = $('input[name="id"]').val();
                $.callAjaxHtml('post', adminUrl+'/product/onAttribute', params, $('.form-box-content'), function(res) {
                    $('#attribute-list').html(res);
                    var attributeSelectDiv = $('.attribute-select2');
                    attributeSelectDiv.select2();
                    attributeSelectDiv.val($.getMultiSelectChosen('property-chosen')).trigger('change');
                    var productAttributeTbody = document.getElementById('product-attribute-tbody');
                    Sortable.create(productAttributeTbody);
                });
            };
            /*initial attribute by attribute group id*/
            var attributeSetId = attributeSetIdDiv.val();
            $.displayAttribute(attributeSetId);

            attributeSetIdDiv.change(function() {
                var attributeSetId = $(this).val();
                $.displayAttribute(attributeSetId);
            });
        }

        //edit attribute in product edit page
        if (currentUrl.match(/product/) && currentUrl.match(/edit/)) {
            var attributeSelectDiv = $('.attribute-select2');
            attributeSelectDiv.select2();
            attributeSelectDiv.val($.getMultiSelectChosen('property-chosen')).trigger('change');
        }

        if (currentUrl.match(/product/) && currentUrl.match(/edit/)) {
            attributeSetIdDiv.attr('disabled', true);
        }

        /*Check if option chosen*/
        $.checkIfOptionChosen = function(optionId) {
            var optionIdChosenArray = [];
            $('.option-id-chosen').each(function() {
                optionIdChosenArray.push($(this).val());
            });
            var rs = optionIdChosenArray.indexOf(optionId);
            if (rs !== -1) {
                $.displayMsg(msgJs.option_id_chosen, 'error', 1000);
                throw msgJs.option_id_chosen;
            }
        };

        /*Choose option*/
        $('#option-choose-select').on('select2:select', function (e) {
            var data = e.params.data;
            var optionTypeJson = $('#option-type-json').text();
            var optionTypeObj = JSON.parse(optionTypeJson);
            var optionId = data.id;
            var optionType = optionTypeObj[optionId];
            var optionName = data.text;
            var optionLi = '<li class="product-option-li active" id="product-option-li-'+optionId+'"' +
                ' option-id="'+optionId+'">' +
                '<i class="fa fa-minus-circle" aria-hidden="true"></i>' +
                ''+optionName+'</li>';
            if (optionId != '0') {
                $.checkIfOptionChosen(optionId);
                $('.product-option-li').removeClass('active');
                var chosen = '<input type="hidden" class="option-id-chosen" id="option-id-chosen-'+optionId+'" value="'+optionId+'" />';
                $('#option-chosen').append(chosen);
                $('#prepend-option').prepend(optionLi);
                $('#option-choose-select').val(0).trigger('change');
                var params = {};
                params._token = tokenGenerate;
                params.option_id = optionId;
                params.option_type = optionType;
                $.callAjaxHtml('post', adminUrl+'/product/onAppendOption', params, $('.form-box-content'), function(res) {
                    $('.option-value').hide();
                    $('#append-option-value').append(res);
                    var optionValueTbody = document.getElementById('tbody-option-value-'+optionId);
                    Sortable.create(optionValueTbody);
                });
            }
        });
        /*append value delete*/
        jQuery.appendValueDelete = function(productToOptionDelete) {
            var appendValueDelete = '<input type="hidden" name="product_to_option_delete_id[]"' +
                ' value="' + productToOptionDelete + '"/>';
            $('#option-value-delete').append(appendValueDelete);
        };
        /*Remove option value*/
        $(document).on('click', '.remove-option-value', function() {//remove option value
            var productToOptionDelete = $(this).attr('product-to-option');
            $.appendValueDelete(productToOptionDelete);
            $(this).parent().parent().remove();
        });
        /*Remove option*/
        $(document).on('click', '.remove-option', function() {//remove option
            var optionId = $(this).attr('attr-option-id');
            $('#tbody-option-value-'+optionId).find('.remove-option-value').each(function() {
                var productToOptionDelete = $(this).attr('product-to-option');
                $.appendValueDelete(productToOptionDelete);
            });
            var divOptionLiId =  $('#product-option-li-'+optionId);
            var optionClosest = divOptionLiId.closest('li').siblings().attr('option-id');
            $('#product-option-li-'+optionClosest).trigger('click');//active closest li
            divOptionLiId.remove();//remove li click trash icon
            $('#div-option-'+optionId).remove();

        });
        /*Add option*/
        $(document).on('click', '.option-plus', function() {
            var params = {};
            params._token = tokenGenerate;
            params.optionValueSelect = $(this).next().text();
            var optionId = $(this).attr('attr-option-id');
            params.optionId = optionId;
            params.optionType = $('#option-type-'+optionId).val();
            $.callAjaxHtml('post', adminUrl+'/product/onOptionPlus', params, $('.form-box-content'), function(res) {
                if (res == gb.fail) {
                    $.displayMsg(msgJs.option_not_have_value, 'error', 1000);
                } else {
                    $('#tbody-option-value-'+optionId).append(res);
                }
            });
        });

        $(document).on('click', '.product-option-li', function() {
            $('.product-option-li').removeClass('active');
            $(this).addClass('active');
            var optionId = $(this).attr('option-id');
            $('.option-value').hide();
            $('#div-option-'+optionId).show();
        });

        var propertySearchUrl = adminUrl+"/product/onSearchProperty";
        $.select2Autocomplete('#property-search', propertySearchUrl);

        /*gallery of product*/
        $('.image-gallery-find').click(function() {
            $.createGallery('product_gallery', 'product-image');
        });
        var productGallery = document.getElementById('product_gallery');
        Sortable.create(productGallery);
        /*gallery of product*/

        /*VARIANT*/

        /*Check variant exists*/
        jQuery.checkVariantExist = function(property) {
            $('.div-item').each(function() {
                var propertyString = $(this).attr('attr-property-str');
                var propertyArrayExisted = propertyString.split(SEPARATE_STRING);
                if (property.length == propertyArrayExisted.length) {
                    //if property need to check equal property existed => continue to check
                    var rs = array_diff(property, propertyArrayExisted);
                    if (rs.length == 0) {
                        $.displayMsg(msgJs.variant_existed, 'error', 1000);
                        throw msgJs.variant_existed;//die here if variant existed
                    }
                }
            });
        };

        /*Call modal variant*/
        jQuery.modalVariant = function(res) {
            divModal.html(res);
            divModal.modal();
            $(document).on('click', '.variant-image', function() {
                $.openKCFinder($(this), 'variant-image');
            });
            /*gallery of variant*/
            $('.variant-gallery-find').click(function() {
                $.createGallery('variant_gallery', 'variant-image');
            });
            var variantGallery = document.getElementById('variant_gallery');
            Sortable.create(variantGallery);
            /*gallery of variant*/
        };

        /*hide or show image and gallery of variant*/
        jQuery.isDisplayImageGalleryOfVariant = function() {
            var isVariantChangeImage = $('input[name="is_variant_change_image"]:checked').val();
            var NO = $('#const-no').val();
            if (isVariantChangeImage == NO) {
                $('#image-variant-form-group, #gallery-variant-form-group').hide();
            }
        };

        /*Handle property, sort property by attribute id*/
        jQuery.sortPropertyByAttributeId = function(property) {
            var attributeProperty = [];
            var attributeArray = [];
            var propertyArraySort = [];
            for (var i=0; i<property.length; i++) {
                var propertyArray = property[i];
                var valueArray = propertyArray.split(SEPARATE_STRING);//0:attribute id, 1:property id
                attributeProperty['attributeId-'+valueArray[0]] = valueArray[1];
                attributeArray.push(valueArray[0]);
            }
            var attributeArraySort = attributeArray.sort();//sort attributeArray
            //assign propertyArraySort based on attributeArraySort
            $.each(attributeArraySort, function(index, value) {
                propertyArraySort.push(attributeProperty['attributeId-'+value]);
            });
            return propertyArraySort;
        };

        /*Display modal to create variant*/
        $('#create-variant').click(function() {
            var property = $('#property-search').val();
            if (property.length == 0) {
                $.displayMsg(msgJs.empty_property, 'error',1000);
            } else {
                if (property.length > 1) {
                    //more than 1 property
                    property = $.sortPropertyByAttributeId(property);
                } else {
                    //just 1 property
                    var propertyArray = property[0].split(SEPARATE_STRING);
                    property[0] = propertyArray[1];
                }
                $.checkVariantExist(property);
                var params = {};
                params._token = tokenGenerate;
                params.property = property;
                $.callAjaxHtml('post', adminUrl+'/product/onModalVariant', params, $('#variant'), function(res) {
                    $.modalVariant(res);
                    $.isDisplayImageGalleryOfVariant();
                });
            }
        });

        /*Update variant*/
        jQuery.updateVariant = function(variantIndex, formData) {
            var variantJsonIndexDiv = $('#variant-json-'+variantIndex);
            formData.id_update = variantJsonIndexDiv.attr('id-update');
            var gallery = '';
            $.each(formData.variant_gallery, function(index, value) {
                gallery = value;//just get value of object gallery
            });
            if (typeof gallery == 'object') {
                gallery = gallery.join(SEPARATE_STRING)
            }
            formData.variant_gallery = gallery;
            variantJsonIndexDiv.text(JSON.stringify(formData));
            var divVariantTrIndex = $('#variant-tr-'+variantIndex);
            divVariantTrIndex.find('.qty-variant').text($('#qty_variant').val());
            divVariantTrIndex.find('.price-variant').text($('#price_variant').val());
            divModal.modal('hide');
        };

        /*append variant to list*/
        $(document).on('click', '#button-save-variant', function() {
            var formVariant = $('#form-variant');
            var formData = formVariant.serializeJSON();
            var property = $('#property-search').select2('data');
            var propertyArray = [];
            for (var i=0; i<property.length; i++) {
                var obj = {id:property[i].id, text:property[i].text};
                propertyArray.push(obj);
            }
            var params = {};
            params._token = tokenGenerate;
            params.formData = formData;
            params.propertyArray = propertyArray;
            params.index = $('.div-item').length + 1;//start from 1, to avoid 0 to know create or update
            var variantIndex = $(this).attr('attr-index');
            if (variantIndex == 0) {//create ~ append to list
                $.callAjaxHtml('post', adminUrl+'/product/onAppendVariant', params, $('#variant'), function(res) {
                    $('#div-append-item').append(res);
                    $('#property-search').val(null).trigger("change");
                    divModal.modal('hide');
                });
            } else {//update
                $.updateVariant(variantIndex, formData);
            }
        });

        /*remove variant*/
        $(document).on('click', '.remove-variant', function(e) {
            e.stopPropagation();
            $(this).parent().remove();
            var i = 1;
            $('.div-item').each(function() {
                $(this).attr('attr-index', i);
                $(this).attr('id', 'variant-tr-'+i);
                i++;
            })
        });

        /*Update variant*/
        $(document).on('click', '.div-item', function() {
            var params = {};
            params._token = tokenGenerate;
            params.index = $(this).attr('attr-index');
            var variantJsonData = $(this).find('.variant-json-data').text();
            params.variant = JSON.parse(variantJsonData);
            $.callAjaxHtml('post', adminUrl+'/product/onModalVariantEdit', params, $('#variant'), function(res) {
                $.modalVariant(res);
                $.isDisplayImageGalleryOfVariant();
            });
        });
        /*VARIANT*/

        if (currentUrl.match(/product/) && currentUrl.match(/edit/)) {
            $('#category\\[\\]').val($.getMultiSelectChosen('category-chosen')).trigger('change');
            $('#product_label\\[\\]').val($.getMultiSelectChosen('label-chosen')).trigger('change');
            var productOptionLi = $('.product-option-li');
            productOptionLi.eq(0).addClass('active');
            var optionIdActive = productOptionLi.eq(0).attr('option-id');
            $('.option-value').hide();
            $('#div-option-'+optionIdActive).show();
            $('.tbody-option-value').each(function() {
                var idDivName = $(this).attr('id');
                var optionValueTbody = document.getElementById(idDivName);
                Sortable.create(optionValueTbody);
            });

        }

        //remove attribute
        $(document).on('click', '.remove-attribute', function() {
            $(this).parent().parent().remove();
        });

        //add attribute
        $(document).on('click', '.attribute-plus', function() {
            var params = {};
            params._token = tokenGenerate;
            $.callAjaxHtml('post', adminUrl+'/product/onModalAddAttribute', params, $('#attribute'), function(res) {
                divModal.html(res);
                $('#add-attribute-select').select2();
                divModal.modal();
            });
        });

        $(document).on('click', '#btn-add-attribute', function() {
            var params = {};
            params._token = tokenGenerate;
            var attributeData = $('#add-attribute-select').select2('data');
            params.id = attributeData[0].id
            params.attributeName = attributeData[0].text;
            $.callAjaxHtml('post', adminUrl+'/product/onAddAttribute', params, $('#attribute'), function(res) {
                $('#product-attribute-tbody').append(res);
                $('.attribute-select2').select2();
                divModal.modal('hide');
            });
        });
    }
    /**
     * END PRODUCT
     */

    /*Order*/
    if (currentUrl.match(/order/) && currentUrl.match(/edit/)) {
        //change payment status
        $('.btn-not-paid, .btn-paid').click(function() {
            var params = {};
            params.id = $(this).attr('attr-order-id');
            params.payment_status = $(this).attr('attr-status-change');
            params._token = tokenGenerate;
            $.alertable.confirm(msgJs.change_payment_status, params).then(function() {
                $.callAjax('post', adminUrl+'/order-change-payment-status', params, $('#order-manager-div'), function(res) {
                    if (res.rs == gb.fail) {
                        $.displayMsg(res.msg[0], 'error', 1000);
                    } else {
                        location.reload();
                    }
                });
            });
        });

        //change order history
        $('#change-order-history').click(function(e) {
            e.preventDefault();
            var params = {};
            params.form = $('#form-add-order-history').serializeArray();
            params._token = tokenGenerate;
            $.callAjax('post', adminUrl+'/order-change-order-status', params, $('#order-manager-div'), function(res) {
                if (res.rs == gb.fail) {
                    $.displayMsg(res.msg[0], 'error', 1000);
                } else {
                    location.reload();
                }
            });
        });

    }
    /*Order*/

    /*PERMISSION*/
    $(document).on('click', '.check-all-control', function() {
        var controller = $(this).attr('attr-controller');
        $('.checkbox-'+controller).attr('checked', true);
    });
    /*PERMISSION*/

    /*BACKEND USER*/
    if (currentUrl.match(/backend_user/) && currentUrl.match(/create/) ||
        currentUrl.match(/backend_user/) && currentUrl.match(/edit/)) {
        var roleSelect = $('#role_id');
        //change permission by role
        jQuery.changePermissionByRole = function(roleId) {
            var params = {};
            params.roleId = roleId;
            params._token = tokenGenerate;
            $.callAjaxHtml('post', adminUrl+'/BackendUser/onChangePermission', params, $('.form-box-content'), function(res) {
                $('#permission').html(res);
            });
        };
        //initial when create
        if (currentUrl.match(/backend_user/) && currentUrl.match(/create/)) {
            var roleId = roleSelect.val();
            $.changePermissionByRole(roleId);
        }
        //change role
        roleSelect.change(function() {
            var roleIdChange = $(this).val();
            $.changePermissionByRole(roleIdChange);
        });
        //Send reset password email
        $('#send-reset-password-email').click(function() {
            var params = {};
            params._token = tokenGenerate;
            params.email = $(this).attr('attr-email');
            params.id = $(this).attr('attr-id');
            $.callAjaxHtml('post', adminUrl+'/BackendUser/onSendResetPasswordEmail', params, $('.form-box-content'), function(res) {
                if (res.rs == gb.fail) {
                    $.displayMsg(res.msg[0], 'error', 1000);
                } else {
                    location.reload();
                }
            });
        });
    }
    /*BACKEND USER*/

    /*PAGE*/
    if (currentUrl.match(/page/) && currentUrl.match(/create/) ||
        currentUrl.match(/page/) && currentUrl.match(/edit/) ) {
        $.initTinyMce('body');
    }
    /*PAGE*/

    /*BLOCK*/
    if (currentUrl.match(/block/) && currentUrl.match(/create/) ||
        currentUrl.match(/block/) && currentUrl.match(/edit/) ) {
        $.initTinyMce('content');
    }
    /*BLOCK*/

    /*MAIL*/
    if (currentUrl.match(/mail/) && currentUrl.match(/edit/) ) {
        var content = document.getElementById('mail_content_text').innerText;
        var viewHtml = document.getElementById('mail_content_preview');
        viewHtml.innerHTML = content ;
    }
    /*MAIL*/

    /**
     * CATEGORY
     */
    if (currentUrl.match(/category/)) {
        var categoryTree = $('#category-tree');
        var optionTree = {
            data: JSON.parse($('#data-tree').text()),
            dragAndDrop: true,
            autoOpen: true,
            onCreateLi: function(node, $li) {
                //insert folder icon before title
                $li.find('.jqtree-title').before(
                    '<i class="tree-icon tree-themeicon"></i>'
                );
            }
        };
        var treeInitial = categoryTree.tree(optionTree);
        categoryTree.on(
            'tree.click',
            function(event) {
                // The clicked node is 'event.node'
                event.preventDefault();//prevent default event when click
                var node = event.node;
                var nodeId = node.id;
                var controllerName = $('#controller-name').val();
                window.location.href = adminUrl+'/'+controllerName+'?id='+nodeId;
            }
        );
        //reorder update
        jQuery.reOrderCategory = function(idMove, idParentMoveTo, siblingPrevId, siblingNextId) {
            var data = {};
            data._token = tokenGenerate;
            data.idMove = idMove;
            data.parent_id = idParentMoveTo;
            data.siblingPrevId = siblingPrevId;
            data.siblingNextId = siblingNextId;
            $.ajax({
                type : 'POST',
                url : adminUrl + '/category-re-order-update',
                data : data,
                beforeSend: function() {
                    $('body').waitMe({color:waitMeColor})
                },
                success: function(res){
                    $('body').waitMe('hide');
                    if (res.rs == gb.fail) {
                        $.displayMsg(res.msg[0], 'error', 1000);
                    } else {
                        location.reload();
                    }
                }
            });
        };
        //drag and drop node
        categoryTree.on(
            'tree.move',
            function(event) {
                var moveElemId = event.move_info.moved_node.id;
                var targetId = event.move_info.target_node.id;
                var position = event.move_info.position;
                var parentId = 0, siblingPrevId = 0, siblingNextId = 0;
                if (position == 'inside') {
                    parentId = targetId;
                } else if (position == 'after') {
                    siblingPrevId = targetId;
                } else {
                    siblingNextId = targetId;
                }
                $.reOrderCategory(moveElemId, parentId, siblingPrevId, siblingNextId);
            }
        );
        //expand and collapsed tree
        $('.tree-toggle').click(function() {
            var action = $(this).attr('data-action');
            var parentString = $('#parent-id-string').text();
            if (parentString != '') {
                var parentArray = parentString.split(SEPARATE_STRING);
                if (action == 'collapsed') {
                    $.each(parentArray, function (index, value) {
                        var node = treeInitial.tree('getNodeById', value);
                        treeInitial.tree('closeNode', node);
                    });
                } else {
                    $.each(parentArray, function (index, value) {
                        var node = treeInitial.tree('getNodeById', value);
                        treeInitial.tree('openNode', node);
                    });
                }
            }
        });
        //create sub category
        $('#create-sub-category').click(function() {
            var parentId = $(this).attr('attr-parent-id');
            var controllerName = $('#controller-name').val();
            if (parentId == '') {
                $.displayMsg(msgJs.choose_parent_category, 'error', 1000);
            } else {
                window.location.href = adminUrl+'/'+controllerName+'?parent_id='+parentId;
            }
        });

        var categoryIdEdit = $('#category-id').val();
        if (categoryIdEdit != 0) {
            var node = treeInitial.tree('getNodeById', categoryIdEdit);
            treeInitial.tree('selectNode', node);
            $('#create-sub-category').attr('attr-parent-id', categoryIdEdit).addClass('btn-blue').removeClass('btn-default');
            $('#button-delete').addClass('btn-warning').attr('attr-id', categoryIdEdit);
        }

        /*Remove category*/
        $('#button-delete').click(function(e) {
            e.preventDefault();
            if ($(this).hasClass('btn-warning')) {
                var catId = $(this).attr('attr-id');
                var params = {};
                params.id = catId;
                params._token = tokenGenerate;
                $.alertable.confirm(msgJs.delete_record, params).then(function() {
                    $.callAjax('post', adminUrl+'/category-delete', params, $('#category-reorder'), function(res) {
                        if (res.rs == gb.fail) {
                            $.displayMsg(res.msg[0], 'error', 1000);
                        } else {
                            var controllerName = $('#controller-name').val();
                            window.location.href = adminUrl+'/'+controllerName;
                        }
                    });
                });
            }
        });
    }


    /**
     * END CATEGORY
     */

    /**
     * END BACKEND
     */



});