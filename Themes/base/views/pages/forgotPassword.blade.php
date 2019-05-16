@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.user.forgot_password') }}
@stop

@section('content')

<div class="container form-div">
    <div class="row">
        {{Form::open(['url'=>'/', 'id'=>'form-forgot-password'])}}
        <div class="col-md-6">
            <h3>{{ Theme::lang('lang.user.account') }}</h3>
            <p>Enter the e-mail address associated with your account.
                Click 'Send mail' to have a password reset link e-mailed to you.</p>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.email') }}</label>
                <span class="required">*</span>
                <input name="email" type="text" class="form-control">
            </div>
            <button type="submit" class="btn btn-black btn_save"
                    attr-form-action="onForgotPassword"
                    attr-form="form-forgot-password">
                {{ Theme::lang('lang.user.send_mail') }}
            </button>
        </div>
        {{ Form::close() }}
    </div>
</div>

@stop