<li class="list-group-item">
    <input
            required
            class="magic-radio"
            type="radio"
            name="shipping_method"
            id="shipping-method-{{ $shippingKey }}-{{ $shippingOption }}"
            @if (old('shipping_method', $shippingKey) == $defaultShippingMethod && old('shipping_option', $shippingOption) == $defaultShippingOption) checked @endif
            value="{{ $shippingKey }}"
            data-option="{{ $shippingOption }}"
    >
    <label for="shipping-method-{{ $shippingKey }}-{{ $shippingOption }}">{{ $shippingItem['name'] }} - {{ format_price($shippingItem['price']) }}</strong></label>
</li>
