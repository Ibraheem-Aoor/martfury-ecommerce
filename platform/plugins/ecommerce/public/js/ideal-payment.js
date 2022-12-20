    // Loop over each funding source/payment method
    paypal.Marks({
        fundingSource: paypal.FUNDING.IDEAL
    }).render('#ideal-mark');
    let button;
    button = paypal.PaymentFields({
        fundingSource: paypal.FUNDING.IDEAL,
        style: {
            // style object (optional)
        },
    }).render('#ideal-container');;
    paypal.Buttons({
        fundingSource: paypal.FUNDING.IDEAL,
        style: {
            label: "pay",
        },
        createOrder(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        currency: 'EUR',
                        value: total_amount,
                    }
                }]
            });
        },
        onApprove(data, actions) {
            return actions.order.capture().then(function (orderData) {
                $('input[type="radio"][name="payment_method"]').val('iDEAL');
                $('#checkout-btn-custom').click();
                $('#checkout-btn-custom').attr('disabled', 'disabled');
            });
        },
        onCancel(data, actions) {
            // console.log(`Order Canceled - ID: ${data.orderID}`);
            toastr.error("{{ __('Payment failed!') }}");
        },
        onError(err) {
            toastr.error("{{ __('Payment failed!') }}");
        }
    }).render("#ideal-btn");
