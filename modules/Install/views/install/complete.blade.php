@extends('Install.View::layout.main')

@section('content')
<h3>3.{{ __('Install.Lang::lang.general.complete') }}</h3>
<div class="text-center complete-title">
    <i class="fa fa-check-circle-o fa-check-circle-o-complete"></i>
    <p class="text-20">{{ __('Install.Lang::lang.msg.install_success') }}</p>
</div>
<div class="row complete-go-to">
    <div class="col-md-5 complete-go-div">
        <a href="{{ $baseUrl }}" target="_blank" class="go-to">
            <i class="fa fa-desktop" aria-hidden="true"></i>
            <p class="text-16">{{ __('Install.Lang::lang.msg.go_to_shop') }}</p>
        </a>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-5 complete-go-div">
        <a href="{{ $adminUrl }}" target="_blank" class="go-to">
            <i class="fa fa-cog" aria-hidden="true"></i>
            <p class="text-16">{{ __('Install.Lang::lang.msg.log_in_admin') }}</p>
        </a>
    </div>
</div>
@stop