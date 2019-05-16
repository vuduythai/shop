@extends('Backend.View::layout.main')

@section('title')
{{ __('Backend.Lang::lang.permission.not_permission') }}
@endsection

@section('content')


<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{{ __('Backend.Lang::lang.permission.not_permission') }}</h3>
    </div>
    <div class="box-body box-permission">
        {{ __('Backend.Lang::lang.permission.can_not_access_route') }}.
        {{ __('Backend.Lang::lang.permission.return') }}
        <a href="{{route('adminDashboard')}}">
            {{ __('Backend.Lang::lang.general.dashboard') }}
        </a>
    </div>
</div>

@stop