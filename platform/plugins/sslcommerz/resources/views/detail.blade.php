@if ($payment)
    @php
        $data = Arr::get($payment, 'element.0');
    @endphp
    <hr>
    <p>{{ trans('plugins/payment::payment.payment_id') }}: {{ Arr::get($data, 'tran_id') }}</p>
    <p>{{ trans('plugins/payment::payment.amount') }}: {{ Arr::get($data, 'currency_amount') . ' ' . Arr::get($data, 'currency_type') }}</p>
    <p>{{ trans('plugins/payment::payment.status') }}: {{ Arr::get($data, 'status') }}</p>
    <p>{{ trans('plugins/payment::payment.card') }}: {{ Arr::get($data, 'card_issuer') }}</p>
    <p>{{ trans('plugins/payment::payment.country') }}: {{ Arr::get($data, 'card_issuer_country') }}</p>
    <p>{{ trans('core/base::tables.created_at') }}: {{ now()->parse(Arr::get($data, 'tran_date')) }}</p>
    <hr>
    @if ($refunds = Arr::get($paymentModel->metadata, 'refunds', []))
        <h6 class="alert-heading">{{ trans('plugins/payment::payment.amount_refunded') . ':' }}
            {{ collect($refunds)->sum('_data_request.refund_amount') }} {{ $paymentModel->currency }}</h6>

        @foreach ($refunds as $refund)
            <div id="{{ Arr::get($refund, 'refund_ref_id') }}">
                @include('plugins/sslcommerz::refund-detail')
            </div>
        @endforeach
    @endif
    @include('plugins/payment::partials.view-payment-source')
@endif
