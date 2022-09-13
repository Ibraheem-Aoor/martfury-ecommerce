<div class="alert alert-success" role="alert">
    <h4 class="alert-heading">{{ $title ?? __('You will receive money through the information below') }}</h4>
    @if (Arr::get($bankInfo, 'name'))
        <p>{{ __('Bank Name') }}: <strong>{{ Arr::get($bankInfo, 'name') }}</strong></p>
    @endif
    @if (Arr::get($bankInfo, 'code'))
        <p>{{ __('Bank Code/IFSC') }}: <strong>{{ Arr::get($bankInfo, 'code') }}</strong></p>
    @endif
    @if (Arr::get($bankInfo, 'full_name'))
        <p>{{ __('Account Holder Name') }}: <strong>{{ Arr::get($bankInfo, 'full_name') }}</strong></p>
    @endif
    @if (Arr::get($bankInfo, 'number'))
        <p>{{ __('Account Number') }}: <strong>{{ Arr::get($bankInfo, 'number') }}</strong></p>
    @endif
    @if (Arr::get($bankInfo, 'paypal_id'))
        <p>{{ __('PayPal ID') }}: <strong>{{ Arr::get($bankInfo, 'paypal_id') }}</strong></p>
    @endif
    @if (Arr::get($bankInfo, 'upi_id'))
        <p>{{ __('UPI ID') }}: <strong>{{ Arr::get($bankInfo, 'upi_id') }}</strong></p>
    @endif
    @if (Arr::get($bankInfo, 'description'))
        <p>{{ __('Description') }}: {{ Arr::get($bankInfo, 'description') }}</p>
    @endif
    @isset($link)
        <hr>
        <p class="mb-0">{!! clean(__('You can update in <a href=":link">here</a>', ['link' => $link])) !!}</p>
    @endisset
</div>
