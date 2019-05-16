@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.login') }}
@stop

@section('content')

<div class="container form-div">
    <div class="row">
        {{Form::open(['url'=>'/', 'id'=>'form-login'])}}
        <input type="hidden" name="redirect_url" value="/" />
        <div class="col-md-6">
            <h3>{{ Theme::lang('lang.user.return_customer') }}</h3>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.email') }}</label>
                <span class="required">*</span>
                <input name="email" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.password') }}</label>
                <span class="required">*</span>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-black btn_save"
                    attr-form-action="onLogin"
                    attr-form="form-login">
                {{ Theme::lang('lang.general.login') }}
            </button>
            <a href="/forgot-password" class="btn btn-black">
                {{ Theme::lang('lang.user.forgot_password') }}
            </a>
        </div>
        {{ Form::close() }}
        <div class="col-md-6">
            <h3><b>{{ Theme::lang('lang.user.new_customer') }}</b></h3>
            <b>{{ Theme::lang('lang.general.register') }}</b>
            <p>
                By creating an account you will be able to shop faster,
                be up to date on an order's status,
                and keep track of the orders you have previously made.
            </p>
            <a href="/register" class="btn btn-black">
                {{ Theme::lang('lang.general.register') }}
            </a>
        </div>
    </div>
</div>

@stop