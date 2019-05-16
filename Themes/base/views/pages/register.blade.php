@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.register') }}
@stop

@section('content')

<div class="container form-div">
    {{Form::open(['url'=>'/', 'id'=>'form-register'])}}
    <div class="row">
        <h3 class="text-center">{{ Theme::lang('lang.user.account') }}</h3>
        <p class="text-center">If you already have an account, please login at the <a href="/login">login page.</a> </p>
        <div class="col-md-6">
            <h4>{{ Theme::lang('lang.user.basic_info') }}</h4>
            <hr/>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.first_name') }}</label>
                <span class="required">*</span>
                <input name="first_name" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.last_name') }}</label>
                <span class="required">*</span>
                <input name="last_name" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.password') }}</label>
                <span class="required">*</span>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.retype_password') }}</label>
                <span class="required">*</span>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <h4>{{ Theme::lang('lang.user.address') }}</h4>
            <hr/>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.email') }}</label>
                <span class="required">*</span>
                <input name="email" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.phone') }}</label>
                <span class="required">*</span>
                <input name="phone" type="number" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.address') }}</label>
                <span class="required">*</span>
                <input name="address" type="text" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-black btn_save"
                    attr-form-action="onRegister"
                    attr-form="form-register">
                {{ Theme::lang('lang.general.register') }}
            </button>
        </div>
    </div>
    {{ Form::close() }}
</div>

@stop
