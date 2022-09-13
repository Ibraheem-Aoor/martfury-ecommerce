<div class="bg-light p-2">
    <p class="font-weight-bold mb-0">{{ __('Product(s)') }}:</p>
</div>
<div class="checkout-products-marketplace" id="shipping-method-wrapper">
    @foreach ($groupedProducts as $grouped)
        @php
            $cartItems = $grouped['products']->pluck('cartItem');
            $store = $grouped['store'];
            if (!$store->exists) {
                $store->id = 0;
                $store->name = theme_option('site_title');
                $store->logo = theme_option('logo');
            }
            $storeId = $store->id;
            $sessionData = Arr::get($sessionCheckoutData, 'marketplace.' . $storeId, []);
            $shipping = Arr::get($sessionData, 'shipping', []);
            $defaultShippingOption = Arr::get($sessionData, 'shipping_option');
            $defaultShippingMethod = Arr::get($sessionData, 'shipping_method');
            $promotionDiscountAmount = Arr::get($sessionData, 'promotion_discount_amount', 0);
            $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount', 0);
            $shippingAmount = Arr::get($sessionData, 'shipping_amount', 0);
            $isFreeShipping = Arr::get($sessionData, 'is_free_shipping', 0);
            $rawTotal = Cart::rawTotalByItems($cartItems);
            $shippingCurrent = Arr::get($shipping, $defaultShippingMethod . '.' . $defaultShippingOption, []);
        @endphp
        <div class="mt-3 bg-light mb-3">
            <div class="p-2" style="background: antiquewhite;">
                <img src="{{ RvMedia::getImageUrl($store->logo, 'small', false, RvMedia::getDefaultImage()) }}"
                    alt="{{ $store->name }}"
                    class="img-fluid rounded"
                    width="30">
                <span class="font-weight-bold">{{ $store->name }}</span>
                @if (EcommerceHelper::isReviewEnabled())
                    <div class="rating_wrap">
                        <div class="rating">
                            <div class="product_rate" style="width: {{ 4 * 20 }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-3">
                @foreach($grouped['products'] as $product)
                    @include('plugins/ecommerce::orders.checkout.product', ['product' => $product, 'cartItem' => $product->cartItem])
                @endforeach
            </div>

            <hr>
            @if (count($groupedProducts) > 1)
                <div class="p-3">
                    <div class="row">
                        <div class="col-6">
                            <p>{{ __('Subtotal') }}:</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="price-text sub-total-text text-end"> {{ format_price(Cart::rawSubTotalByItems($cartItems)) }} </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <p>{{ __('Shipping fee') }}:</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="price-text">
                                @if (Arr::get($shippingCurrent, 'price') && $isFreeShipping)
                                    <span class="font-italic" style="text-decoration-line: line-through;">{{ format_price(Arr::get($shippingCurrent, 'price')) }}</span>
                                    <span class="font-weight-bold">{{ __('Free shipping') }}</span>
                                @else
                                    <span class="font-weight-bold">{{ format_price(Arr::get($shippingCurrent, 'price')) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if (EcommerceHelper::isTaxEnabled())
                        <div class="row">
                            <div class="col-6">
                                <p>{{ __('Tax') }}:</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="price-text tax-price-text">{{ format_price(Cart::rawTaxByItems($cartItems)) }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($couponDiscountAmount)
                        <div class="row">
                            <div class="col-6">
                                <p>{{ __('Discount amount') }}:</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="price-text tax-price-text">{{ format_price($couponDiscountAmount) }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-6">
                            <p>{{ __('Total') }}:</p>
                        </div>
                        <div class="col-6 float-end">
                            <p class="total-text raw-total-text mb-0"
                            data-price="{{ Cart::rawTotalByItems($cartItems) }}">
                                {{ ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > $rawTotal ? format_price(0) : format_price($rawTotal - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="shipping-method-wrapper p-3">

                @if (!empty($shipping))
                    <div class="payment-checkout-form">
                        <div class="mx-0">
                            <h6>{{ __('Shipping method') }}:</h6>
                        </div>

                        <input type="hidden" name="shipping_option[{{ $storeId }}]" value="{{ old("shipping_option.$storeId", $defaultShippingOption) }}">
                        <div id="shipping-method-{{ $storeId }}">
                            <ul class="list-group list_payment_method">
                                @foreach ($shipping as $shippingKey => $shippingItem)
                                    @foreach($shippingItem as $subShippingKey => $subShippingItem)
                                        @include('plugins/marketplace::orders.partials.shipping-option', [
                                            'defaultShippingMethod' => $defaultShippingMethod,
                                            'defaultShippingOption' => $defaultShippingOption,
                                            'shippingOption'        => $subShippingKey,
                                            'shippingItem'          => $subShippingItem,
                                            'storeId'               => $storeId
                                        ])
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <p>{{ __('No shipping methods available!') }}</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
