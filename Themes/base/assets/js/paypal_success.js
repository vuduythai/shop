$(document).ready(function() {
    var tokenGenerate = $('#token_generate').val();
    //paypal success
    if (document.getElementById('paypal_success') !== null) {
        var tokenPaypal = sessionStorage.getItem('token_paypal');
        sessionStorage.setItem('token_paypal', 0);//reset to 0
        var paypalTokenGet = $('#paypal_token').text();
        if (tokenPaypal == paypalTokenGet) {//save order
            var params = {};
            params.token = tokenPaypal;
            params._token = tokenGenerate;
            $.callAjax('post', '/onPaypalSuccess', params, $('#checkout-cart-div'), function(res) {
                console.log(res);
            });
        } else {
            document.location.href = baseUrl+'/paypal-cancel';
        }
    }
});