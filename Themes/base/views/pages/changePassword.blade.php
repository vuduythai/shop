@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.user.change_password') }}
@stop

@section('content')

<div class="container form-div">
    <div class="row">
        {{Form::open(['url'=>'/', 'id'=>'form-change-password'])}}
        <div class="col-md-6">
            <h3>{{ Theme::lang('lang.user.change_password') }}</h3>
            <hr/>
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
            <button type="submit" class="btn btn-black btn_save"
                    attr-form-action="onChangePassword"
                    attr-form="form-change-password">
                {{ Theme::lang('lang.general.submit') }}
            </button>
        </div>
        {{ Form::close() }}
    </div>
</div>

@stop