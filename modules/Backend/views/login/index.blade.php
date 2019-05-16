<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Backend.Lang::lang.general.login') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @php
    $favicon = '';
    if (isset($share['config']['favicon'])) {
        $favicon = $share['config']['favicon'];
    }
    @endphp
    <link rel="shortcut icon" href="@imageDisplay($favicon)" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('/vendor/css/mix_login.css') }}">
    <link rel="stylesheet" href="{{ asset('/modules/backend/assets/css/login.css') }}">
</head>

<body class="hold-transition login-page">

<div id="token_generate" style="display: none;">{{csrf_token()}}</div>
<input type="hidden" id="admin_url" value="{{URL::to('/'.config('app.admin_url'))}}" />
<input type="hidden" id="result-success" value="{{ \Modules\Backend\Core\System::SUCCESS}}" />
<input type="hidden" id="result-fail" value="{{ \Modules\Backend\Core\System::FAIL}}" />
<input type="hidden" id="action-create" value="{{ \Modules\Backend\Core\System::ACTION_CREATE}}" />
<input type="hidden" id="action-update" value="{{ \Modules\Backend\Core\System::ACTION_UPDATE}}" />
<input type="hidden" id="folder-image" value="{{ \Modules\Backend\Core\System::FOLDER_IMAGE}}" />
<input type="hidden" id="wait-me-color" value="{{ \Modules\Backend\Core\System::WAIT_ME_COLOR}}" />

<div class="login-box">
    <div id="header-login">
    </div>
    <input type="hidden" id="base_url" value="{{URL::to('/')}}" />
    <div class="login-box-body">
        {{ Form::open(array('url' => '/do-login', 'id'=>'form-login', 'class'=>'form-login')) }}
            <h3 id="shop-title" class="text-center">Ideas Shop</h3>
            <div class="form-group has-feedback">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="hr-10"></div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password"
                       id="password" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="hr-10"></div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat" id="sign-in-button">Sign In</button>
                </div>
            </div>
            <div class="hr-20"></div>
        {{ Form::close() }}
    </div><!-- .login-box-body -->
</div><!-- .login-box -->

<script src="{{ asset('/vendor/js/mix.js') }}"></script>

<script>
$(document).ready(function() {
    $('#form-login').submit(function(e) {//catch event click submit button and click keyboard enter
        var adminUrl = $('#admin_url').val();
        e.preventDefault();//prevent form submit
        var params = {};
        var form = $('#form-login');
        params.formData = form.serializeArray();
        params._token = $('#token_generate').text();
        var SUCCESS = $('#result-success').val();
        var ERROR = $('#result-fail').val();
        $.ajax({
            method: 'post',
            url: adminUrl+'/do-login',
            data : params,
            beforeSend: function() {
                $('.login-box').waitMe({color: $('#wait-me-color').val()});
            },
            success: function(res) {
                $('.login-box').waitMe('hide');
                if (res.rs == ERROR) {
                    $.displayMsg(res.msg[0], 'danger', 1000);
                } else {
                    window.location.href = adminUrl + '/';
                }
            }
        });
    });
});
</script>

</body>
</html>
