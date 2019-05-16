@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.user.order_detail') }}
@stop

@section('content')

<div class="container" id="order-manager-div">
    <h3 class="text-center">{{ Theme::lang('lang.order.order_detail') }}</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="order-product-title">
                <div class="text-bold pull-left p-order-product-title">{{ Theme::lang('lang.order.order') }}</div>
            </div>
            <table class="table-bordered table-order table-order-detail">
                <thead>
                <tr>
                    <th>{{ Theme::lang('lang.order.billing_address') }}</th>
                    <th>{{ Theme::lang('lang.order.shipping_address') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</td>
                    <td>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</td>
                </tr>
                <tr>
                    <td>{{ $order->billing_address }}</td>
                    <td>{{ $order->shipping_address }}</td>
                </tr>
                <tr>
                    <td>{{ $order->billing_phone }}</td>
                    <td>{{ $order->shipping_phone }}</td>
                </tr>
                <tr>
                    <td>{{ $order->billing_email }}</td>
                    <td>{{ $order->shipping_email }}</td>
                </tr>
                <tr>
                    <td colspan="2">{{ Theme::lang('lang.order.shipping') }} :
                        {{ $order->ship->name }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <div class="order-product-title">
                <div class="text-bold pull-left p-order-product-title">{{ Theme::lang('lang.order.product') }}</div>
            </div>
            <table class="table-bordered table-order table-order-detail">
                <thead>
                <tr>
                    <th>{{ Theme::lang('lang.order.product') }}</th>
                    <th>{{ Theme::lang('lang.order.qty') }}</th>
                    <th>{{ Theme::lang('lang.order.price_after_tax') }}</th>
                    <th>{{ Theme::lang('lang.order.total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->product as $product)
                <tr>
                    <td>
                        {{ $product->name }}<br/>
                        @if (!empty($order->option))
                            @foreach ($order->option as $option)
                                @if ($option->product_id == $product['product_id'])
                                <span>- {{ $option->option_name }} : </span>
                                <span class="text-grey">{{ $option->value_name }}</span>
                                <br/>
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $product->qty }}</td>
                    <td>@displayPriceAndCurrency($product->price_after_tax)</td>
                    <td>@displayPriceAndCurrency($product->qty * $product->price_after_tax)</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3">{{ Theme::lang('lang.order.shipping_cost') }}</td>
                    <td>@displayPriceAndCurrency($order->shipping_cost)</td>
                </tr>
                <tr>
                    <td colspan="3">{{ Theme::lang('lang.order.total') }}</td>
                    <td>@displayPriceAndCurrency($order->total)</td>
                </tr>
                </tbody>
            </table>
            <a href="{{ $urlPrintInvoice }}" class="pull-right btn btn-black" id="btn-print-invoice" target="_blank">
                {{ Theme::lang('lang.order.print_invoice') }}
            </a>
        </div>
    </div>

</div>

@stop