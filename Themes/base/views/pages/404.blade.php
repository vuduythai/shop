@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.general.404') }}
@stop

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="pnf-div">
                <p class="pnf-title">404</p>
                <p class="pnf-message">Oops! Page not found</p>
                <p class="pnf-content">Sorry, but your page you are looking is not found. Return homepage <a href="/">here</a></p>
            </div>
        </div>
    </div>
</div>

@stop