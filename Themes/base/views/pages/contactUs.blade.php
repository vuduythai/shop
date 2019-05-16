@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.contact_us') }}
@stop

@section('content')

<div class="container">
    <div class="row form-div">
        <div class="col-md-6">
            <h3>{{ Theme::lang('lang.general.contact_us') }}</h3>
            <div class="hr-10"></div>
            <span class="grey-small">{{ Theme::lang('lang.contact_us.contact_us_msg') }}</span>
            <div class="hr-20"></div>
            {{Form::open(['url'=>'/', 'id'=>'form-contact-us'])}}
            <div class="form-group">
                <label>{{ Theme::lang('lang.contact_us.name') }}</label>
                <span class="required">*</span>
                <input name="name" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.email') }}</label>
                <span class="required">*</span>
                <input name="email" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.user.phone') }}</label>
                <input name="phone" type="number" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ Theme::lang('lang.contact_us.message') }}</label>
                <span class="required">*</span>
                <textarea class="form-control" name="message"></textarea>
            </div>
            <input type="hidden" name="store_email" value="{{ $config['store_email'] }}" />
            <button type="submit" class="btn btn-black btn_save"
                    attr-form-action="onContactUs"
                    attr-form="form-contact-us">
                {{ Theme::lang('lang.user.send_mail') }}
            </button>
            {{ Form::close() }}
        </div>
        <div class="col-md-6">
            <h3>{{ Theme::lang('lang.contact_us.store_info') }}</h3>
            <div class="hr-10"></div>
            <div class="store-info">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <p class="store-info-title">{{ Theme::lang('lang.user.phone') }}</p>
                <p class="text-grey store-info-data">{{ $config['store_phone'] }}</p>
            </div>
            <div class="store-info">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <p class="store-info-title">{{ Theme::lang('lang.user.email') }}</p>
                <p class="text-grey store-info-data">{{ $config['store_email'] }}</p>
            </div>
            <div class="store-info">
                <i class="fa fa-home" aria-hidden="true"></i>
                <p class="store-info-title">{{ Theme::lang('lang.user.address') }}</p>
                <p class="text-grey store-info-data">{{ $config['store_address'] }}</p>
            </div>
        </div>
    </div>
</div>

@stop
