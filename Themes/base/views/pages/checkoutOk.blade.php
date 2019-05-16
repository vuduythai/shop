@extends('layouts.main')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{ Theme::lang('lang.checkout.checkout_success') }} <br/>
            {{ Theme::lang('lang.checkout.order_send') }}
        </div>
    </div>
</div>


@stop
