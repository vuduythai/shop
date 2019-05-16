/**
 * One to many form
 * Event: onCreateItemModalOne, onUpdateItemModalOne
 */
var currentUrl = window.location.href;
var baseUrl = window.location.origin;
var gb = globalObject();
var msgJsArray = gb.msgJs;
var adminUrl = $('#admin_url').val();
var divModal = $('#divFillModal');
var controller = $('#controller-name').val();
var regController = new RegExp(controller, "");
var itemFactoryModelName = $('#item-factory-model-name').val();
var divItemName = '.div-item';

jQuery.appendItemEmpty = function() {
    var divAppendItem = $('#div-append-item');
    var numTh = $('#table-item thead tr th').length;
    var divAppend = '<tr id="no-record-item"><td colspan="'+numTh+'"><p>'+msgJsArray.no_record+'</p></td></tr>';
    divAppendItem.append(divAppend);
};

jQuery.thereIsNoItem = function() {
    if ($(divItemName).length == 0) {
        $('#check-all-items').trigger('click');
        $.appendItemEmpty();
    }
};

jQuery.removeNoItemDiv = function() {
    $('#no-record-item').remove();
};

jQuery.createItem = function() {
    var params = {};
    params._token = $('#token_generate').text();
    params.action = gb.action_create;
    params.controller = controller;
    if (currentUrl.match(/attribute/)) {
        params.type = $('#type').val();
    }
    params.index = parseInt($(divItemName).length) + 1;
    $.callAjaxHtml('post', adminUrl+'/one/onCreateItem', params, $('.form-box-content'), function(res) {
        divModal.html(res);
        divModal.modal();
        //disable enter key when create item
        $(document).keypress(
            function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
        });
        $(document).trigger('onCreateItemModalOne');//event
    });
};

/**
 * append new item to list
 */
jQuery.appendNewItemToList = function(id, action, itemData, index) {
    var params = {};
    params._token = $('#token_generate').text();
    params.id = id;
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    params.action = action;
    params.itemData = itemData;
    if (action == gb.action_create) {
        params.index = parseInt($(divItemName).length) + 1;
    } else {
        params.index = index;
    }
    $.callAjaxHtml('post', adminUrl+'/one/onAppendItem', params, $('#form-save-item'), function(res){
        if (params.action == gb.action_update) {
            $('#div-item-index-'+index).replaceWith(res);
        } else {
            $('#div-append-item').append(res);
        }
        divModal.modal('hide');
    });
};

/**
 * Save item
 */
jQuery.saveItem = function(action, id, index) {
    var params = {};
    var form = $('#form-save-item');
    params.formData = form.serializeArray();
    params._token = $('#token_generate').text();
    params.action = action;
    params.id = id;
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    $.callAjax('post', adminUrl+'/one/onStoreItem', params, form, function(res) {
        if (res.rs == gb.fail) {
            $.displayMsg(res.msg[0], 'danger', 1000);
        } else {
            $.removeNoItemDiv();
            $.appendNewItemToList(res.id, params.action, res.itemData, index);
        }
    });
};

/**
 * Get item to edit parent
 */
jQuery.getItemToEditParent = function() {
    var params = {};
    var form = $('#form-save-item');
    params.id = $('input[name="id"]').val();
    params._token = $('#token_generate').text();
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    $.callAjax('post', adminUrl+'/one/onEditParent', params, form, function(res) {
        $('#div-append-item').append(res);
    });
};


/**
 * Update item
 */
jQuery.updateItem = function(itemData, index, id) {
    var params = {};
    params.action = gb.action_update;
    params.controller = controller;
    params._token = $('#token_generate').text();
    params.itemData = itemData;
    params.index = index;
    params.id = id;
    $.callAjaxHtml('post', adminUrl+'/one/onUpdateItem', params, $('.form-box-content'), function(res) {
        divModal.html(res);
        divModal.modal();
        //disable enter key when update item
        $(document).keypress(
            function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
        $(document).trigger('onUpdateItemModalOne');//event
    });
};

/**
 * Delete items
 */
jQuery.deleteItem = function(data) {
    var params = {};
    params.idArray = data.idArray;
    params._token = $('#token_generate').text();
    params.modelItem = itemFactoryModelName;
    $.callAjaxHtml('post', adminUrl+'/one/onDeleteItem', params, $('.form-box-content'), function(res) {
        var resObj = JSON.parse(res);
        if (resObj.rs == gb.success) {
            $.displayMsg(msgJsArray.delete_success, 'success', 1000);
            var indexArray = data.indexArray;
            for (var i=0; i<indexArray.length; i++) {
                $('#div-item-index-'+indexArray[i]).remove();
            }
            $.thereIsNoItem();
        }
    });
};

/**
 * Get id check to delete
 */
jQuery.getItemChecked = function(callback) {
    var idArray = [];
    var indexArray = [];
    $('.item-checkbox').each(function() {
        if ($(this).is(':checked')) {
            idArray.push($(this).val());
            indexArray.push($(this).attr('attr-index'));
        }
    });
    if (idArray.length == 0) {
        $.displayMsg(msgJsArray.choose_one, 'danger', 1000);
    } else {
        //callback
        if (typeof callback == 'function') {
            callback.call(this, {idArray:idArray, indexArray:indexArray});//pass response to callback
        }
    }
};

$(document).ready(function() {
    if (currentUrl.match(regController) && currentUrl.match(/create/)) {
        $.appendItemEmpty();
    }

    $(document).on('click', '.image-finder', function() {
        $.openKCFinder($(this), 'item-hidden');
    });

    $(document).on('click', '#save-item', function() {
        var action = $(this).attr('attr-action');
        var id = $(this).attr('attr-id');
        var index = $(this).attr('attr-index');
        $.saveItem(action, id, index);
    });

    $('#create-item').click(function() {
        $.createItem();
    });

    /**
     * Before save assign for textarea name 'item_json_data' id='item-json-data'
     * use event beforeClickButtonSaveData in backend.js
     */
    $(document).on('beforeClickButtonSaveData', function() {
        var itemData = [];
        $('.item-json-data').each(function() {
            itemData.push(JSON.parse($(this).text()));
        });
        $('#item-json-data').text(JSON.stringify(itemData));
    });

    /**
     * Edit parent
     */
    if (currentUrl.match(regController) && currentUrl.match(/edit/)) {
        $.getItemToEditParent();
    }

    $(document).on('click', divItemName, function() {
        var itemData = $(this).find('.item-json-data').text();
        var index = $(this).attr('attr-index');
        var id = $(this).attr('attr-id');
        $.updateItem(itemData, index, id);
    });

    $(document).on('click', '.item-checkbox-not-update', function(e) {
        //stop event when click to parent class '.div-item'
        e.stopPropagation();
    });

    /**
     * Delete item
     */
    $('#delete-item').click(function() {
        $.getItemChecked(function(res) {
            $.alertable.confirm(msgJsArray.sure_to_delete, res).then(function() {
                $.deleteItem(res);
            });
        });
    });


    // Sortable rows
    var el = document.getElementById('div-append-item');
    var sortItem = Sortable.create(el);

    //check all item
    $('#check-all-items').click(function() {
        $.toggleAllCheckBox(this, 'item-checkbox');
    });

    //check all attach items checkbox
    $(document).on('click', '#attach-all-items-checkbox', function() {
        $.toggleAllCheckBox(this, 'attach-item-checkbox');
    });


});
