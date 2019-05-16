<style>
    #invoice-div {
        margin: 0 auto;
        max-width: 1024px;
    }
    .table-invoice {
        width: 100%;
        border: 1px #cccccc solid;
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    .table-invoice tr th{
        border: 1px #cccccc solid;
    }
    .table-invoice tr td {
        border-right: 1px #cccccc solid;
    }
    .table-invoice tr td {
        padding-left: 10px;
    }
    .table-invoice tr th p {
        float: left;
        margin-left: 10px;
        font-weight: bold;
    }
    .no-border-right {
        border-right: none !important;
    }
    .table-product tr td {
        border-bottom: 1px #cccccc solid;
        padding: 10px;
    }
    .text-bold {
        font-weight: bold;
    }
</style>
<div id='invoice-div'>
    <h3>INVOICE #{{ $order->id }}</h3>
    <table class='table-invoice'>
        <thead>
        <tr>
            <th colspan='2'>Order Detail</p></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                Telephone 123456789<br/>
                E-Mail admin@admin.to<br/>
                Web Site: http://ideasshop.local<br/>
            </td>
            <td>
                Date Added {{$now}}<br/>
                Order ID: {{ $order->id }}<br/>
                Payment Method : {{ $payment_method_name }}<br/>
                Shipping Method : {{ $shipping_rule_name }}<br/>
            </td>
        </tr>

        </tbody>
    </table>
    <table class='table-invoice'>
        <thead>
        <tr>
            <th><p>Billing Address</p></th>
            <th><p>Shipping Address</p></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $order->billing_name }}</td>
            <td>{{ $order->shipping_name }}</td>
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
            <td>Shipping :
                {{ $shipping_rule_name }}</td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <table class='table-invoice table-product'>
        <thead>
        <tr>
            <th><p>Product</p></th>
            <th><p>Quantity</p></th>
            <th><p>Price after tax</p></th>
            <th><p>Total</p></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($product as $row)
        <tr>
            <td>
                {{ $row->name }}
            </td>
            <td>{{ $row->qty }}</td>
            <td>@displayPriceAndCurrency($row->price_after_tax)</td>
            <?php $priceWithQty = $row->qty * $row->price_after_tax ?>
            <td>@displayPriceAndCurrency($priceWithQty)</td>
        </tr>
        @endforeach
        <tr>
            <td class='no-border-right'></td>
            <td class='no-border-right'></td>
            <td class='text-bold'>Shipping Cost</td>
            <td>@displayPriceAndCurrency($order->shipping_cost)</td>
        </tr>
        <tr>
            <td class='no-border-right'></td>
            <td class='no-border-right'></td>
            <td class='text-bold'>Total</td>
            <td>@displayPriceAndCurrency($order->total)</td>
        </tr>
        </tbody>
    </table>
</div>