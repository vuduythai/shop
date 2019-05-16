@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.checkout.paypal_success') }}
@stop

@section('content')
<div id="paypal_token" style="display: none;">{{$tokenPaypal}}</div>
<div id="paypal_success"><!-- to trigger event save order --></div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{ Theme::lang('lang.checkout.paypal_paid_success') }}
        </div>
    </div>
</div>

@stop

@section('scripts')
<script src="{{ themes('js/paypal_success.js') }}"></script>
@stop
