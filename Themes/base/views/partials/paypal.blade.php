<form action="{{ $paypalUrl }}" method="post" id="paypal_method_form">
    <!-- Identify your business so that you can collect the payments. -->
    <input type="hidden" name="business" value="{{ $config['paypal_id'] }}">

    <!-- Specify a Buy Now button. -->
    <input type="hidden" name="cmd" value="_xclick">

    <!-- Specify details about the item that buyers will purchase. -->
    <input type="hidden" name="item_name" id="paypal_item_name" value="test">
    <input type="hidden" name="item_number" id="paypal_item_qty" value="1">
    <input type="hidden" name="amount" id="total_amount_paypal" value="10">
    <input type="hidden" name="currency_code" id="currency_code_paypal" value="USD">

    <!-- Specify URLs -->
    <input type='hidden' name='cancel_return' value='{{$paypalCancel}}'>
    <input type='hidden' name='return' id="url_success" value='{{$paypalSuccess}}'>

</form>