$(document).ready(function() {
    var baseUrl = window.location.origin;
    var waitMeColor = '#17171e';
    var tokenGenerate = $('#token_generate').text();
    var resultFail = $('#result-fail').val();
    var msgJs = JSON.parse($('#msg_js').text());

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

    /**
     * Function to call ajax
     */
    jQuery.callAjax = function(method, url, data, divWaitMe, waitMeText, callback) {
        $.ajax({
            method: method,
            url: url,
            data : data,
            beforeSend: function() {
                divWaitMe.waitMe({color: waitMeColor, text:waitMeText});
            },
            success: function(response) {
                divWaitMe.waitMe('hide');
                if (typeof callback == 'function') {
                    callback.call(this, response);//pass response to callback
                }
            }
        });
    };

    jQuery.validateThenSave = function(e, action, divWaitMe) {
        $(document).trigger('beforeClickButtonSaveData');//create event beforeClickButtonSaveData
        e.preventDefault();
        var params = {};
        params._token = tokenGenerate;
        var formDynamic = $('.form_dynamic');
        params.formData = formDynamic.serializeJSON();
        divWaitMe || (divWaitMe = $('#content'));//default is $('.form-box-content')
        $.callAjax('post', action, params, divWaitMe, msgJs.wait_me_install,function(res) {
            if (res.rs == resultFail) {
                $.displayMsg(res.msg[0], 'error', 1000);
            } else {
                window.location.href = baseUrl+'/install/complete';
            }
        });
    };

    $('#save-configuration').click(function(e){
        $.validateThenSave(e, baseUrl+'/install/validate-config');
    });
});