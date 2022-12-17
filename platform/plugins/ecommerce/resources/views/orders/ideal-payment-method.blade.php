<script
    src="https://www.paypal.com/sdk/js?client-id=AYDZxTFB6Jz0yVef5t9wn4sRhrRRZPbYCwCl9Q7aVKjc8-_MTRC7tBZwm6dmHGy1L_H-Y20kbIAsrVB-&components=buttons,payment-fields,marks,funding-eligibility&enable-funding=ideal&currency=EUR" async>
</script>
<li class="list-group-item">
    <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_ideal" value="ideal"
        data-bs-toggle="collapse" data-bs-target=".payment_ideal_wrap" data-parent=".list_payment_method">
    <label for="payment_ideal" class="text-start">iDEAL</label>
    <div class="payment_ideal_wrap payment_collapse_wrap show" style="padding: 15px 0;" id="ideal-root">
        <div id="ideal-container">
            <span id="ideal-mark"></span>
            <div id="ideal-btn"></div>
        </div>
    </div>
</li>
<script src="{{ asset('vendor/core/plugins/ecommerce/js/ideal-payment.js') }}" async></script>
