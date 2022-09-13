<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('plugins/ecommerce::order.invoice_for_order') }} {{ get_order_code($order->id) }}</title>

    @if (get_ecommerce_setting('using_custom_font_for_invoice', 0) == 1 && get_ecommerce_setting('invoice_font_family'))
        <link href="https://fonts.googleapis.com/css?family={{ urlencode(get_ecommerce_setting('invoice_font_family')) }}:400,500,600,700,900&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/ecommerce/css/invoice.css') }}?v=1.1.2">

    <style>
        body {
            font-size: 15px;
            font-family: '{{ get_ecommerce_setting('using_custom_font_for_invoice', 0) == 1 ? get_ecommerce_setting('invoice_font_family', 'DejaVu Sans') : 'DejaVu Sans' }}', Arial, sans-serif !important;
        }
    </style>
</head>
<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>

@if (get_ecommerce_setting('enable_invoice_stamp', 1) == 1)
    <span class="stamp @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED) is-failed @elseif ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED) is-completed @else is-failed @endif">
        @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
            {{ $order->status->label() }}
        @else
            {{ $order->payment->status->label() }}
        @endif
    </span>
@endif

@php
    $logo = theme_option('logo_in_invoices') ?: theme_option('logo');
@endphp

<table class="invoice-info-container">
    <tr>
        <td>
            <div class="logo-container">
                @if ($logo)
                    <img src="{{ RvMedia::getImageUrl($logo) }}"
                         style="width:100%; max-width:150px;" alt="{{ theme_option('site_title') }}">
                @endif
            </div>
        </td>
        <td>
            <p><strong>{{ now()->format('F d, Y') }}</strong></p>
            <p>{{ trans('plugins/ecommerce::order.invoice') }} <strong>{{ get_order_code($order->id) }}</p>
        </td>
    </tr>
</table>

<table class="invoice-info-container">
    <tr>
        <td>
            <p>{{ get_ecommerce_setting('store_name') }}</p>
            <p>{{ get_ecommerce_setting('store_address') }}, {{ get_ecommerce_setting('store_city') }}, {{ get_ecommerce_setting('store_state') }}, {{ \Botble\Base\Supports\Helper::getCountryNameByCode(get_ecommerce_setting('store_country')) }}</p>
            <p>{{ get_ecommerce_setting('store_phone') }}</p>
            @if (get_ecommerce_setting('store_vat_number'))
                <p>{{ trans('plugins/ecommerce::ecommerce.setting.vat_number') }}: {{ get_ecommerce_setting('store_vat_number') }}</p>
            @endif
        </td>
        <td>
            <p>{{ $order->address->name }}</p>
            <p>{{ $order->full_address }}</p>
            @if ($order->address->phone)
                <p>{{ $order->address->phone }}</p>
            @endif
        </td>
    </tr>
</table>


<table class="line-items-container">
    <thead>
    <tr>
        <th class="heading-description">{{ trans('plugins/ecommerce::products.form.product') }}</th>
        <th class="heading-description">{{ trans('plugins/ecommerce::products.form.options') }}</th>
        <th class="heading-quantity">{{ trans('plugins/ecommerce::products.form.quantity') }}</th>
        <th class="heading-price">{{ trans('plugins/ecommerce::products.form.price') }}</th>
        <th class="heading-subtotal">{{ trans('plugins/ecommerce::products.form.total') }}</th>
    </tr>
    </thead>
    <tbody>

        @foreach ($order->products as $orderProduct)
            @php
                $product = get_products([
                    'condition' => [
                        'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
                        'ec_products.id' => $orderProduct->product_id,
                    ],
                    'take' => 1,
                    'select' => [
                        'ec_products.id',
                        'ec_products.images',
                        'ec_products.name',
                        'ec_products.price',
                        'ec_products.sale_price',
                        'ec_products.sale_type',
                        'ec_products.start_date',
                        'ec_products.end_date',
                        'ec_products.sku',
                    ],
                ]);
            @endphp
            @if (!empty($product))
                <tr>
                    <td>
                        {{ $product->name }}
                    </td>
                    <td>
                        <small>{{ $product->variation_attributes }}</small>

                        @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                            @foreach($orderProduct->options as $option)
                                @if (!empty($option['key']) && !empty($option['value']))
                                    <p class="mb-0">
                                        <small>{{ $option['key'] }}:
                                            <strong> {{ $option['value'] }}</strong></small>
                                    </p>
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        {{ $orderProduct->qty }}
                    </td>
                    <td class="right">
                        @if ($product->front_sale_price != $product->price)
                            {!! htmlentities(format_price($product->front_sale_price)) !!}
                            <del>{!! htmlentities(format_price($product->price)) !!}</del>
                        @else
                            {!! htmlentities(format_price($product->price)) !!}
                        @endif
                    </td>
                    <td class="bold">
                        @if ($product->front_sale_price != $product->price)
                            {!! htmlentities(format_price($product->front_sale_price * $orderProduct->qty)) !!}
                        @else
                            {!! htmlentities(format_price($product->price * $orderProduct->qty)) !!}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach

        <tr>
            <td colspan="4" class="right">
                {{ trans('plugins/ecommerce::products.form.sub_total') }}
            </td>
            <td class="bold">
                {!! htmlentities(format_price($order->sub_total)) !!}
            </td>
        </tr>
        @if (EcommerceHelper::isTaxEnabled())
            <tr>
                <td colspan="4" class="right">
                    {{ trans('plugins/ecommerce::products.form.tax') }}
                </td>
                <td class="bold">
                    {!! htmlentities(format_price($order->tax_amount)) !!}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="4" class="right">
                {{ trans('plugins/ecommerce::products.form.shipping_fee') }}
            </td>
            <td class="bold">
                {!! htmlentities(format_price($order->shipping_amount)) !!}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="right">
                {{ trans('plugins/ecommerce::products.form.discount') }}
            </td>
            <td class="bold">
                {!! htmlentities(format_price($order->discount_amount)) !!}
            </td>
        </tr>
    </tbody>
</table>


<table class="line-items-container">
    <thead>
    <tr>
        <th>{{ trans('plugins/ecommerce::order.payment_info') }}</th>
        <th>{{ trans('plugins/ecommerce::order.total_amount') }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="payment-info">
            <div>
                {{ trans('plugins/ecommerce::order.payment_method') }}: <strong>{{ $order->payment->payment_channel->label() }}</strong>
            </div>
            <div>
                {{ trans('plugins/ecommerce::order.payment_status_label') }}: <strong>{{ $order->payment->status->label() }}</strong>
            </div>
        </td>
        <td class="large total">{!! htmlentities(format_price($order->amount)) !!}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
