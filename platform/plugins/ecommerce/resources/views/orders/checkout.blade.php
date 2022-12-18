@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Checkout') }}
@stop
@section('content')

    @if (Cart::instance('cart')->count() > 0)
        <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.5">
        <script src="{{ asset('vendor/core/plugins/payment/js/payment.js') }}?v=1.0.5"></script>

        {!! Form::open([
            'route' => ['public.checkout.process', $token],
            'class' => 'checkout-form payment-checkout-form',
            'id' => 'checkout-form',
        ]) !!}
        <input type="hidden" name="checkout-token" id="checkout-token" value="{{ $token }}">

        <div class="container" id="main-checkout-product-info">
            <div class="row">
                <div class="order-1 order-md-2 col-lg-5 col-md-6 right">
                    <div class="d-block d-sm-none">
                        @include('plugins/ecommerce::orders.partials.logo')
                    </div>
                    <div id="cart-item" class="position-relative">

                        <div class="payment-info-loading" style="display: none;">
                            <div class="payment-info-loading-content">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>

                        <!---------------------- RENDER PRODUCTS IN HERE ---------------- -->
                        {!! apply_filters(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, $products) !!}

                        <div class="mt-2 p-2">
                            <div class="row">
                                <div class="col-6">
                                    <p>{{ __('Subtotal') }}:</p>
                                </div>
                                <div class="col-6">
                                    <p class="price-text sub-total-text text-end">
                                        {{ format_price(Cart::instance('cart')->rawSubTotal()) }} </p>
                                </div>
                            </div>
                            @if (session('applied_coupon_code'))
                                <div class="row coupon-information">
                                    <div class="col-6">
                                        <p>{{ __('Coupon code') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text coupon-code-text"> {{ session('applied_coupon_code') }} </p>
                                    </div>
                                </div>
                            @endif
                            @if ($couponDiscountAmount > 0)
                                <div class="row price discount-amount">
                                    <div class="col-6">
                                        <p>{{ __('Coupon code discount amount') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text total-discount-amount-text">
                                            {{ format_price($couponDiscountAmount) }} </p>
                                    </div>
                                </div>
                            @endif
                            @if ($promotionDiscountAmount > 0)
                                <div class="row">
                                    <div class="col-6">
                                        <p>{{ __('Promotion discount amount') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text"> {{ format_price($promotionDiscountAmount) }} </p>
                                    </div>
                                </div>
                            @endif
                            @if (!empty($shipping))
                                <div class="row">
                                    <div class="col-6">
                                        <p>{{ __('Shipping fee') }}:</p>
                                    </div>
                                    <div class="col-6 float-end">
                                        <p class="price-text shipping-price-text">{{ format_price($shippingAmount) }}</p>
                                    </div>
                                </div>
                            @endif

                            @if (EcommerceHelper::isTaxEnabled())
                                <div class="row">
                                    <div class="col-6">
                                        <p>{{ __('Tax') }}:</p>
                                    </div>
                                    <div class="col-6 float-end">
                                        <p class="price-text tax-price-text">
                                            {{ format_price(Cart::instance('cart')->rawTax()) }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-6">
                                    <p><strong>{{ __('Total') }}</strong>:</p>
                                </div>
                                <div class="col-6 float-end">
                                    <p class="total-text raw-total-text"
                                        data-price="{{ format_price(Cart::instance('cart')->rawTotal(), null, true) }}">
                                        {{ $promotionDiscountAmount + $couponDiscountAmount - $shippingAmount > Cart::instance('cart')->rawTotal() ? format_price(0) : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3 mb-5">
                        @include('plugins/ecommerce::themes.discounts.partials.form')
                    </div>
                </div>
                <div class="col-lg-7 col-md-6 left">
                    <div class="d-none d-sm-block">
                        @include('plugins/ecommerce::orders.partials.logo')
                    </div>
                    <div class="form-checkout">
                        <form action="{{ route('payments.checkout') }}" method="post">
                            @csrf

                            <div>
                                <h5 class="checkout-payment-title">{{ __('Shipping information') }}</h5>
                                <input type="hidden" value="{{ route('public.checkout.save-information', $token) }}"
                                    id="save-shipping-information-url">
                                @include('plugins/ecommerce::orders.partials.address-form',
                                    compact('sessionCheckoutData'))
                            </div>
                            <br>

                            @if (!is_plugin_active('marketplace'))
                                <div id="shipping-method-wrapper">
                                    <h5 class="checkout-payment-title">{{ __('Shipping method') }}</h5>
                                    <div class="shipping-info-loading" style="display: none;">
                                        <div class="shipping-info-loading-content">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                    </div>
                                    @if (!empty($shipping))
                                        <div class="payment-checkout-form">
                                            <input type="hidden" name="shipping_option"
                                                value="{{ old('shipping_option', $defaultShippingOption) }}">
                                            <ul class="list-group list_payment_method">
                                                @foreach ($shipping as $shippingKey => $shippingItem)
                                                    @foreach ($shippingItem as $subShippingKey => $subShippingItem)
                                                        @include('plugins/ecommerce::orders.partials.shipping-option',
                                                            [
                                                                'defaultShippingMethod' => $defaultShippingMethod,
                                                                'defaultShippingOption' => $defaultShippingOption,
                                                                'shippingOption' => $subShippingKey,
                                                                'shippingItem' => $subShippingItem,
                                                            ])
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <p>{{ __('No shipping methods available!') }}</p>
                                    @endif
                                </div>
                                <br>
                            @endif

                            <div class="position-relative">
                                <div class="payment-info-loading" style="display: none;">
                                    <div class="payment-info-loading-content">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                                <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>
                                <input type="hidden" name="amount"
                                    value="{{ $promotionDiscountAmount + $couponDiscountAmount - $shippingAmount > Cart::instance('cart')->rawTotal() ? 0 : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount, null, true) }}">
                                <input type="hidden" name="currency"
                                    value="{{ strtoupper(get_application_currency()->title) }}">
                                <input type="hidden" name="callback_url"
                                    value="{{ route('public.payment.paypal.status') }}">
                                <input type="hidden" name="return_url"
                                    value="{{ \Botble\Payment\Supports\PaymentHelper::getRedirectURL($token) }}">
                                {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}
                                <ul class="list-group list_payment_method">



                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method collapsed" type="radio"
                                            name="payment_method" value="10" data-bs-toggle="collapse"
                                            data-bs-target=".payment_1_wrap" data-parent=".list_payment_method"
                                            aria-expanded="false" checked="checked">
                                        <label class="text-start">
                                            <img src="https://www.borvat.com/payment-images-master//payment_profile_brands/100x100/1.png"
                                                width="100" alt="">
                                            iDEAL</label>
                                        <div class="payment_1_wrap payment_collapse_wrap show collapse"
                                            style="padding: 15px 0px;">
                                            <p>
                                                Met iDEAL kunt u met een Nederlandse bankrekening vertrouwd, veilig en
                                                gemakkelijk betalen via internetbankieren van uw eigen bank.
                                                1
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method"
                                            value="1813" data-bs-toggle="collapse" data-bs-target=".payment_19_wrap"
                                            data-parent=".list_payment_method">
                                        <label class="text-start">
                                            <img src="https://www.borvat.com/payment-images-master//payment_profile_brands/100x100/19.png"
                                                width="100" alt="">
                                            IN3 (in 60 dagen)</label>
                                        <div class="payment_19_wrap payment_collapse_wrap" style="padding: 15px 0;">
                                            <p>
                                                IN3 (in 60 dagen)
                                                2
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method"
                                            value="136" data-bs-toggle="collapse" data-bs-target=".payment_12_wrap"
                                            data-parent=".list_payment_method">
                                        <label class="text-start">
                                            <img src="https://www.borvat.com/payment-images-master//payment_profile_brands/100x100/12.png"
                                                width="100" alt="">
                                            Overboeking (SCT)</label>
                                        <div class="payment_12_wrap payment_collapse_wrap" style="padding: 15px 0;">
                                            <p>
                                                Boek uw betaling over naar ons IBAN rekeningnummer, betalingen worden binnen
                                                2 werkdagen verwerkt.
                                                3
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method"
                                            value="559" data-bs-toggle="collapse" data-bs-target=".payment_4_wrap"
                                            data-parent=".list_payment_method">
                                        <label class="text-start">
                                            <img src="https://www.borvat.com/payment-images-master//payment_profile_brands/100x100/4.png"
                                                width="100" alt="">
                                            Sofortbanking</label>
                                        <div class="payment_4_wrap payment_collapse_wrap" style="padding: 15px 0;">
                                            <p>
                                                Sofortbanking
                                                4
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method"
                                            value="436" data-bs-toggle="collapse" data-bs-target=".payment_2_wrap"
                                            data-parent=".list_payment_method">
                                        <label class="text-start">
                                            <img src="https://www.borvat.com/payment-images-master//payment_profile_brands/100x100/2.png"
                                                width="100" alt="">
                                            Bancontact</label>
                                        <div class="payment_2_wrap payment_collapse_wrap" style="padding: 15px 0;">
                                            <p>
                                                U kunt met Bancontact vertrouwd, veilig en gemakkelijk betalen via
                                                internetbankieren van uw eigen bank, wanneer u een Belgische bankrekening
                                                heeft.
                                                5
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method"
                                            value="694" data-bs-toggle="collapse" data-bs-target=".payment_3_wrap"
                                            data-parent=".list_payment_method">
                                        <label class="text-start">
                                            <img src="https://www.borvat.com/payment-images-master//payment_profile_brands/100x100/3.png"
                                                width="100" alt="">
                                            Giropay</label>
                                        <div class="payment_3_wrap payment_collapse_wrap" style="padding: 15px 0;">
                                            <p>

                                                6
                                            </p>
                                        </div>
                                    </li>


                                </ul>
                            </div>

                            <br>

                            <div class="form-group mb-3 @if ($errors->has('description')) has-error @endif">
                                <label for="description" class="control-label">{{ __('Order notes') }}</label>
                                <br>
                                <textarea name="description" id="description" rows="3" class="form-control"
                                    placeholder="{{ __('Notes about your order, e.g. special notes for delivery.') }}">{{ old('description') }}</textarea>
                                {!! Form::error('description', $errors) !!}
                            </div>

                            @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal())
                                <div class="alert alert-warning">
                                    {{ __('Minimum order amount is :amount, you need to buy more :more to place an order!', ['amount' => format_price(EcommerceHelper::getMinimumOrderAmount()), 'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal())]) }}
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col-md-6 d-none d-md-block" style="line-height: 53px">
                                        <a class="text-info" href="{{ route('public.cart') }}"><i
                                                class="fas fa-long-arrow-alt-left"></i> <span
                                                class="d-inline-block back-to-cart">{{ __('Back to cart') }}</span></a>
                                    </div>
                                    <div class="col-md-6 checkout-button-group">
                                        <button type="submit" @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) disabled @endif
                                            class="btn payment-checkout-btn payment-checkout-btn-step float-end"
                                            data-processing-text="{{ __('Processing. Please wait...') }}"
                                            data-error-header="{{ __('Error') }}">
                                            {{ __('Checkout') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="d-block d-md-none back-to-cart-button-group">
                                    <a class="text-info" href="{{ route('public.cart') }}"><i
                                            class="fas fa-long-arrow-alt-left"></i> <span
                                            class="d-inline-block">{{ __('Back to cart') }}</span></a>
                                </div>
                            </div>
                        </form>

                    </div> <!-- /form checkout -->
                </div>
            </div>
        </div>

        @if (setting('payment_stripe_status') == 1)
            <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/libraries/card/card.css') }}?v=2.5.4">
            <script src="{{ asset('vendor/core/plugins/payment/libraries/card/card.js') }}?v=2.5.4"></script>
            <script src="{{ asset('https://js.stripe.com/v2/') }}"></script>
        @endif
    @else
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning my-5">
                        <span>{!! __('No products in cart. :link!', ['link' => Html::link(route('public.index'), __('Back to shopping'))]) !!}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        $(document).ready(function() {
            $(document).on('click', '.js_payment_method', function() {
                console.log('GG');
                $(this).attr('checked', 'checked');
            });
        });
    </script>
@stop
