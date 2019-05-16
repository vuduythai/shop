@extends('Backend.View::layout.main')
<?php
$controllerNameConvert = ucfirst(str_replace('_', ' ', $controller));
?>
@section('title')
{{ $controllerNameConvert }}
@endsection

@section('content')

@include('Backend.View::share.breadCrumb')


<div class="row">
<div class="col-md-12">
<div id="order-data" style="display: none">{{ json_encode($order) }}</div>
<div id="product-data" style="display: none">{{ json_encode($order->product) }}</div>
<div id="msg_js" style="display: none">{{$msg_js}}</div>

<div class="form-box-shadow">
    <div class="form-box-content">
        <div class="row">
            <div class="col-md-6">
                <div id="order-product-title">
                    <div class="text-bold pull-left p-order-product-title text-18">
                        {{ __('Backend.Lang::lang.order.order') }}
                    </div>
                    <a href="{{ $invoiceUrl }}" id="btn-print-invoice" target="_blank">
                        {{ __('Backend.Lang::lang.order.print_invoice') }}
                    </a>
                </div>
                <table class="table-bordered table-order">
                    <thead>
                    <tr>
                        <th>{{ __('Backend.Lang::lang.order.billing_address') }}</th>
                        <th>{{ __('Backend.Lang::lang.order.shipping_address') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $order['billing_first_name'] }} {{ $order['billing_last_name'] }}</td>
                        <td>{{ $order['shipping_first_name'] }} {{ $order['shipping_last_name'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ $order['billing_address'] }}</td>
                        <td>{{ $order['shipping_address'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ $order['billing_phone'] }}</td>
                        <td>{{ $order['shipping_phone'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ $order['billing_email'] }}</td>
                        <td>{{ $order['shipping_email'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">{{ __('Backend.Lang::lang.order.shipping') }} :
                            {{ $order->ship->name }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <p class="text-bold text-18">{{ __('Backend.Lang::lang.order.add_order_history') }}</p>
                <form id="form-add-order-history">
                    <input type="hidden" value="{{ $id }}" name="id" />
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.order.order_status') }}</label>
                        <select name="order_status_id" class="form-control" id="order-status-change">
                            @foreach ($orderStatus as $row)
                            <option value="{{ $row['id'] }}"
                            {{ $order['order_status_id'] == $row['id'] ? 'selected' : ''}} >
                            {{ $row['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Backend.Lang::lang.order.comment') }}</label>
                        <textarea name="comment" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="change-order-history">
                            {{ __('Backend.Lang::lang.order.submit') }}</button>
                    </div>
                </form>


            </div>
        </div>
        <div class="row order-edit-row" id="order-status-change-div">
            <div class="col-md-6">
                <p class="text-bold text-18">{{ __('Backend.Lang::lang.order.order_payment_status') }}</p>
                @if ($order['payment_status'] ==  \Modules\Backend\Core\System::PAYMENT_STATUS_NOT_PAID)
                <div class="btn-not-paid" attr-order-id="{{ $order['id'] }}" attr-status-change="{{ $paid }}">
                    {{ __('Backend.Lang::lang.order.not_paid') }}
                </div>
                @else
                <div class="btn-paid" attr-order-id="{{ $order['id'] }}" attr-status-change="{{ $notPaid }}">
                    {{ __('Backend.Lang::lang.order.paid') }}
                </div>
                @endif
                <div class="hr-10"></div>
                <p class="text-bold text-18">{{ __('Backend.Lang::lang.order.comment') }}</p>
                <div class="order-comment">
                    <p>{{ $order['comment'] }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <p class="text-bold text-18">{{ __('Backend.Lang::lang.order.order_status_change') }}</p>
                <table class="table-bordered table-order">
                    <thead>
                    <tr>
                        <th>{{ __('Backend.Lang::lang.order.created_at') }}</th>
                        <th>{{ __('Backend.Lang::lang.order.comment') }}</th>
                        <th>{{ __('Backend.Lang::lang.order.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (!empty($order->statusChange))
                    @foreach ($order->statusChange as $row)
                    <tr>
                        <td>{{ IHelpers::dateDisplay($row['created_at']) }}</td>
                        <td>{{ $row['comment'] }}</td>
                        <td>{{ $row->status->name }}</td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row order-edit-row">
            <div class="col-md-12">
                <div id="order-product-title">
                    <div class="text-bold pull-left p-order-product-title text-18">
                        {{ __('Backend.Lang::lang.order.product') }}
                    </div>
                </div>
                <table class="table-bordered table-order">
                    <thead>
                    <tr>
                        <th>{{ __('Backend.Lang::lang.order.product') }}</th>
                        <th>{{ __('Backend.Lang::lang.order.qty') }}</th>
                        <th>{{ __('Backend.Lang::lang.order.price_after_tax') }}</th>
                        <th>{{ __('Backend.Lang::lang.order.total') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($order->product as $row)
                    <tr>
                        <td>
                            <span class="text-16">{{ $row['name'] }}</span> <br/>
                            @if (!empty($order->option))
                            @foreach ($order->option as $option)
                            @if ($option->product_id == $row['product_id'])
                            <span>- {{ $option->option_name }} : </span>
                            <span class="text-grey">{{ $option->value_name }}</span>
                            <br/>
                            @endif
                            @endforeach
                            @endif
                        </td>
                        <td>{{ $row['qty'] }}</td>

                        <td>@displayPriceAndCurrency($row['price_after_tax'])</td>
                        <td>@displayPriceAndCurrency($row['qty'] * $row['price_after_tax'])</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ __('Backend.Lang::lang.order.shipping_cost') }}</td>
                        <td>@displayPriceAndCurrency($order['shipping_cost'])</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ __('Backend.Lang::lang.order.total') }}</td>
                        <td>@displayPriceAndCurrency($order['total'])</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row order-edit-row">
            <div class="col-md-6">
                <a href="{{ URL::to('/'.config('app.admin_url').'/order') }}">
                    {{ __('Backend.Lang::lang.general.cancel') }}
                </a>
            </div>
        </div>
    </div>

</div>

</div>


@stop
