@extends('Backend.View::layout.main')

@section('title')
{{ __('Backend.Lang::lang.general.dashboard') }}
@endsection

@section('content')
@php
$adminUrl = config('app.admin_url')
@endphp
<section class="content">
    <div class="row">

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Backend.Lang::lang.dashboard.total_sales') }}</span>
                    <span class="info-box-number">@displayPriceAndCurrency($order['total'])</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Backend.Lang::lang.dashboard.total_orders') }}</span>
                    <span class="info-box-number">{{ $order['count'] }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Backend.Lang::lang.dashboard.total_customers') }}</span>
                    <span class="info-box-number">{{ $customerCount }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Backend.Lang::lang.dashboard.total_products') }}</span>
                    <span class="info-box-number">{{ $productCount }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

    </div>

    <div class="row">
        <section class="col-md-7">
            <div class="box box-dashboard">
                <div class="box-header">
                    <h3 class="box-title">
                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        {{ __('Backend.Lang::lang.dashboard.sale_analytics') }}
                    </h3>
                </div>

                <div class="box-body">
                    <div id="chart">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
        <section class="col-md-5">
            <div class="box box-dashboard">
                <div class="box-header">
                    <h3 class="box-title">{{ __('Backend.Lang::lang.dashboard.latest_order') }}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-dashboard">
                        <tbody>
                        <tr>
                            <th>{{ __('Backend.Lang::lang.dashboard.order_id') }}</th>
                            <th>{{ __('Backend.Lang::lang.dashboard.customer') }}</th>
                            <th>{{ __('Backend.Lang::lang.dashboard.status') }}</th>
                            <th>{{ __('Backend.Lang::lang.dashboard.total') }}</th>
                        </tr>

                        @foreach ($latestOrder as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->billing_first_name }} {{ $row->billing_last_name }}</td>
                            <td>
                                <span class="badge" style="background-color: {{$row->orderStatus->color}}">
                                {{ $row->orderStatus->name }}
                                </span>
                            </td>
                            <td>@displayPriceAndCurrency($row->total)</td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->

            </div>
        </section>
    </div>

</section>
<div id="sale_analytics" style="display: none">{{ json_encode($orderByCurrentWeek) }}</div>
@stop

@section('scripts')
<script src="{{asset('vendor/js/chart.min.js')}}"></script>
<script>
    function addCommas(nStr)
    {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            datasets: [{
                label: '# of Sales',
                data: JSON.parse(document.getElementById('sale_analytics').innerText),
                backgroundColor: [
                    'rgba(10, 21, 239, 0.4)',
                    'rgba(54, 162, 235, 0.4)',
                    'rgba(255, 206, 86, 0.4)',
                    'rgba(16, 239, 10, 0.4)',
                    'rgba(153, 102, 255, 0.4)',
                    'rgba(239, 10, 70, 0.4)',
                    'rgba(10, 236, 239, 0.4)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        callback: function (value) {
                            return addCommas(value)
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var value = data.datasets[0].data[tooltipItem.index];
                        value = addCommas(value);
                        return value;
                    }
                } // end callbacks:
            } //end tooltips
        }
    });
</script>
@stop