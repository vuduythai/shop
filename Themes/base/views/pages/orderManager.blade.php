@extends('layouts.main')

@section('title')
{{ Theme::lang('lang.user.order_manager') }}
@stop

@section('content')
<div class="container" id="order-manager-div">
    <h3 class="text-center">{{ Theme::lang('lang.order.order_manager') }}</h3>
    <table class="table table-bordered table-order">
        <thead>
            <tr>
                <th> {{ Theme::lang('lang.order.order_id') }} </th>
                <th> {{ Theme::lang('lang.order.date_buy') }} </th>
                <th> {{ Theme::lang('lang.order.product') }} </th>
                <th> {{ Theme::lang('lang.order.total') }} </th>
                <th> {{ Theme::lang('lang.order.status') }} </th>
            </tr>
        </thead>
        <tbody>
        @if (!empty($order))
            @foreach ($order as $row)
                <tr>
                    <td>
                        <a href="/order/detail/{{$row->id}}">
                            {{IHelpers::strPad($row->id)}}
                        </a>
                    </td>
                    <td>{{ IHelpers::dateDisplay($row->created_at) }}</td>
                    <td>
                        @foreach ($row->product as $product)
                        - {{ $product->name }} <br/>
                        @endforeach
                    </td>
                    <td>{{ $row->total }}</td>
                    <td>{{ $row->orderStatus->name }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <div class="text-center">
        <div class="pagination">
            @php
            $link = '/user/order-manager?page=';
            @endphp

            @if ($order->currentPage() != 1)
            <a href="{{ URL::to($link.'1') }}"> << </a>
            <a href="{{ URL::to($link.($order->currentPage() - 1)) }}"> < </a>
            @endif

            @if ($order->lastPage() != 1)
            @for ($i = 1; $i <= $order->lastPage() ; $i++)
            <a href="{{ URL::to($link.$i) }}"
               class="{{ $order->currentPage() == $i ? 'active' : ''}}"
               attr-page="{{$i}}">{{$i}}
            </a>
            @endfor
            @endif

            @if ($order->currentPage() != $order->lastPage())
            <a href="{{ URL::to($link.($order->currentPage() + 1)) }}"> >> </a>
            <a href="{{ URL::to($link.$order->lastPage()) }}"> > </a>
            @endif
        </div>
    </div>
</div>
@stop
