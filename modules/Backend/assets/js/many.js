/**
 * Many to many form
 * Event: onCreateItemModal, onUpdateItemModal
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

jQuery.appendItemEmpty = function() {
    var divAppendItem = $('#div-append-item');
    var numTh = $('#table-item thead tr th').length;
    var divAppend = '<tr id="no-record-item"><td colspan="'+numTh+'"><p>'+msgJsArray.no_record+'</p></td></tr>';
    divAppendItem.append(divAppend);
};

jQuery.thereIsNoItem = function() {
    if ($('.div-item').length == 0) {
        $('#check-all-items').trigger('click');
        $.appendItemEmpty();
    }
};

jQuery.removeNoItemDiv = function() {
    $('#no-record-item').remove();
}

jQuery.createItem = function() {
    var params = {};
    params._token = $('#token_generate').text();
    params.action = gb.action_create;
    params.controller = controller;
    $.callAjaxHtml('post', adminUrl+'/many/onCreateItem', params, $('.form-box-content'), function(res) {
        divModal.html(res);
        divModal.modal();
        $(document).trigger('onCreateItemModal');//event
    });
};

/**
 * append new item to list
 */
jQuery.appendNewItemToList = function(id, action) {
    var params = {};
    params._token = $('#token_generate').text();
    params.id = id;
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    var form = $('#form-save-item');
    params.action = action;
    $.callAjaxHtml('post', adminUrl+'/many/onAppendItem', params, form, function(res){
        if (params.action == gb.action_update) {
            $('#div-item-id-'+params.id).replaceWith(res);
        } else {
            $('#div-append-item').append(res);
        }
        divModal.modal('hide');
    });
};

/**
 * Save item
 */
jQuery.saveItem = function(action, id) {
    var params = {};
    var form = $('#form-save-item');
    params.formData = form.serializeArray();
    params._token = $('#token_generate').text();
    params.action = action;
    params.id = id;
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    $.callAjax('post', adminUrl+'/many/onStoreItem', params, form, function(res) {
        if (res.rs == gb.fail) {
            $.displayMsg(res.msg[0], 'danger', 1000);
        } else {
            $.removeNoItemDiv();
            $.appendNewItemToList(res.id, params.action);
        }
    });
};

/**
 * Assign textare name 'many-items'
 */
jQuery.assignItemField = function() {
    var items = [];
    $('.div-item').each(function() {
        items.push($(this).attr('attr-id'));
    });
    var itemsString = items.join(';');
    $('#many-items').text(itemsString);
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
    $.callAjax('post', adminUrl+'/many/onEditParent', params, form, function(res) {
        $('#div-append-item').append(res);
    });
};

/**
 * Add item
 */
jQuery.attachItem = function(page) {
    var params = {};
    params._token = $('#token_generate').text();
    params.page = page;
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    $.callAjaxHtml('post', adminUrl+'/many/onAttachItem', params, $('#divFillModal'), function(res) {
        divModal.html(res);
        divModal.modal();
    });
};

/**
 * Update item
 */
jQuery.updateItem = function(id) {
    var params = {};
    params.id = id;
    params.action = gb.action_update;
    params.controller = controller;
    params.modelItem = itemFactoryModelName;
    params._token = $('#token_generate').text();
    $.callAjaxHtml('post', adminUrl+'/many/onUpdateItem', params, $('.form-box-content'), function(res) {
        divModal.html(res);
        divModal.modal();
        $(document).trigger('onUpdateItemModal');//event
    });
};

/**
 * Delete items
 */
jQuery.deleteItem = function(idArray) {
    var params = {};
    params.idArray = idArray;
    params._token = $('#token_generate').text();
    params.modelItem = itemFactoryModelName;
    $.callAjaxHtml('post', adminUrl+'/many/onDeleteItem', params, $('.form-box-content'), function(res) {
        var resObj = JSON.parse(res);
        if (resObj.rs == gb.success) {
            $.displayMsg(msgJsArray.delete_success, 'success', 1000);
            for (var i=0; i<idArray.length; i++) {
                $('#div-item-id-'+idArray[i]).remove();
            }
            $.thereIsNoItem();
        }
    });
};

/**
 * Get id check to delete or remove
 */
jQuery.getItemChecked = function(callback) {
    var idArray = [];
    $('.item-checkbox').each(function() {
        if ($(this).is(':checked')) {
            idArray.push($(this).val());
        }
    });
    if (idArray.length == 0) {
        $.displayMsg(msgJsArray.choose_one, 'danger', 1000);
    } else {
        //callback
        if (typeof callback == 'function') {
            callback.call(this, idArray);//pass response to callback
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
        $.saveItem(action, id);
    });

    $('#create-item').click(function() {
        $.createItem();
    });

    /**
     * Before save theme, assign for textarea name 'many-items'
     * use event beforeClickButtonSaveData in backend.js
     */
    $(document).on('beforeClickButtonSaveData', function() {
        $.assignItemField();
    });

    /**
     * Edit parent
     */
    if (currentUrl.match(regController) && currentUrl.match(/edit/)) {
        $.getItemToEditParent();
    }

    /**
     * add item
     */
    $('#attach-item').click(function() {
        $.attachItem(1);
    });

    /**
     * pagination of list item to add
     */
    if (currentUrl.match(regController) && currentUrl.match(/edit/) ||
        currentUrl.match(regController) && currentUrl.match(/create/)) {
        $(document).on('click', '.pagination li a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var params = getParamsInUrlString(url);
            var page = params.page;
            $.attachItem(page);
        })
    }

    /**
     * Choose item
     */
    $(document).on('click', '#choose-item-btn', function() {
        var itemId = [];
        var itemIdExistsInList = [];
        $('.attach-item-checkbox').each(function() {
            if ($(this).is(':checked')) {
                itemId.push($(this).val());
            }
        });
        $('.div-item').each(function() {
            itemIdExistsInList.push($(this).attr('attr-id'));
        });
        if (itemId.length > 0) {
            $.removeNoItemDiv();
            //just add item not in itemIdExistsInList
            for (var i=0; i<itemId.length; i++) {
                if (itemIdExistsInList.indexOf(itemId[i]) === -1) {
                    $.appendNewItemToList(itemId[i], gb.action_create);
                }
            }
            divModal.modal('hide');
        }
    });

    $(document).on('click', '.div-item', function() {
        var id = $(this).attr('attr-id');
        $.updateItem(id);
    });

    $(document).on('click', '.item-checkbox-not-update', function(e) {
        //stop event when click to parent class '.div-item'
        e.stopPropagation();
    });

    /**
     * Delete item
     */
    $('#delete-item').click(function() {
        $.getItemChecked(function(idArray) {
            $.alertable.confirm(msgJsArray.sure_to_delete, idArray).then(function() {
                $.deleteItem(idArray);
            });
        });
    });

    $('#remove-item').click(function() {
        $.getItemChecked(function(idArray) {
            for (var i=0; i<idArray.length; i++) {
                $('#div-item-id-'+idArray[i]).remove();
            }
            $.thereIsNoItem();
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
