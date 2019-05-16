@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.checkout.paypal_cancel') }}
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{ Theme::lang('lang.checkout.paypal_paid_cancel') }}
        </div>
    </div>
</div>

@stop
