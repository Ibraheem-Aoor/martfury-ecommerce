<li class="list-group-item">
    <input
        class="magic-radio shipping_method_input"
        type="radio"
        name="shipping_method[{{ $storeId }}]"
        id="{{ "shipping-method-$storeId-$shippingKey-$shippingOption" }}"
        @if (old('shipping_method.' . $storeId, $shippingKey) == $defaultShippingMethod &&
            old('shipping_option.' . $storeId, $shippingOption) == $defaultShippingOption) checked @endif
        value="{{ $shippingKey }}"
        data-option="{{ $shippingOption }}"
        data-id="{{ $storeId }}">
    <label for="{{ "shipping-method-$storeId-$shippingKey-$shippingOption" }}">
        {{ $shippingItem['name'] }} - 
        @if ($shippingItem['price'] > 0)
            {{ format_price($shippingItem['price']) }}
        @else
            <strong>{{ __('Free shipping') }}</strong>
        @endif
    </label>
</li>
