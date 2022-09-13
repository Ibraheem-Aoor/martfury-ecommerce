@extends(MarketplaceHelper::viewPath('dashboard.layouts.master'))

@section('content')
    <div class="flexbox-layout-sections" id="main-order-content" style="margin: 0 -20px;">
        @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
            <div class="ui-layout__section">
                <div class="ui-layout__item">
                    <div class="ui-banner ui-banner--status-warning">
                        <div class="ui-banner__ribbon">
                            <svg class="svg-next-icon svg-next-icon-size-20">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#alert-circle"></use>
                            </svg>
                        </div>
                        <div class="ui-banner__content">
                            <h2 class="ui-banner__title">{{ trans('plugins/ecommerce::order.order_canceled') }}</h2>
                            <div class="ws-nm">
                                {{ trans('plugins/ecommerce::order.order_was_canceled_at') }} <strong>{{ BaseHelper::formatDate($order->updated_at, 'H:i d/m/Y') }}</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="flexbox-layout-section-primary mt20">
            <div class="ui-layout__item">
                <div class="wrapper-content">
                    <div class="pd-all-20">
                        <div class="flexbox-grid-default">
                            <div class="flexbox-auto-right mr5">
                                <label class="title-product-main text-no-bold">{{ trans('plugins/ecommerce::order.order_information') }} {{ get_order_code($order->id) }}</label>
                            </div>
                        </div>
                        <div class="mt20">
                            @if ($order->shipment->id)
                                <svg class="svg-next-icon svg-next-icon-size-16 next-icon--right-spacing-quartered">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-orders"></use>
                                </svg>
                                <strong class="ml5">{{ trans('plugins/ecommerce::order.completed') }}</strong>
                            @else
                                <svg class="svg-next-icon svg-next-icon-size-16 svg-next-icon-gray">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-order-unfulfilled-16"></use>
                                </svg>
                                <strong class="ml5">{{ trans('plugins/ecommerce::order.completed') }}</strong>
                            @endif
                        </div>
                    </div>
                    <div class="pd-all-20 p-none-t border-top-title-main">
                        <div class="table-wrap">
                            <table class="table-order table-divided">
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
                                                'ec_products.is_variation',
                                            ],
                                        ]);
                                    @endphp

                                    <tr>
                                        @if ($product)
                                            <td class="width-60-px min-width-60-px vertical-align-t">
                                                <div class="wrap-img"><img class="thumb-image thumb-image-cartorderlist" src="{{ RvMedia::getImageUrl($product->image ?: $product->original_product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $orderProduct->product_name }}"></div>
                                            </td>
                                        @endif
                                        <td class="pl5 p-r5 min-width-200-px">
                                            <a class="text-underline hover-underline pre-line" target="_blank" href="{{ $product ? route('marketplace.vendor.products.edit', $product->original_product->id) : '#' }}" title="{{ $orderProduct->product_name }}">
                                                {{ $orderProduct->product_name }}
                                            </a>
                                            @if ($product)
                                                &nbsp;
                                                @if ($product->sku)
                                                    ({{ trans('plugins/ecommerce::order.sku') }}: <strong>{{ $product->sku }}</strong>)
                                                @endif
                                                @if ($product->is_variation)
                                                    <p class="mb-0">
                                                        <small>{{ $product->variation_attributes }}</small>
                                                    </p>
                                                @endif
                                            @endif

                                            @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                                @foreach($orderProduct->options as $option)
                                                    @if (!empty($option['key']) && !empty($option['value']))
                                                        <p class="mb-0"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                    @endif
                                                @endforeach
                                            @endif

                                            {!! apply_filters(ECOMMERCE_ORDER_DETAIL_EXTRA_HTML, null) !!}
                                            @if ($order->shipment->id)
                                                <ul class="unstyled">
                                                    <li class="simple-note">
                                                        <a><span>{{ $orderProduct->qty }}</span><span class="text-lowercase"> {{ trans('plugins/ecommerce::order.completed') }}</span></a>
                                                        <ul class="dom-switch-target line-item-properties small">
                                                            <li class="ws-nm">
                                                                <span class="bull">↳</span>
                                                                <span class="black">{{ trans('plugins/ecommerce::order.shipping') }} </span>
                                                                <strong>{{ $order->shipping_method_name }}</strong>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            @endif
                                        </td>
                                        <td class="pl5 p-r5 text-end">
                                            <div class="inline_block">
                                                <span>{{ format_price($orderProduct->price) }}</span>
                                            </div>
                                        </td>
                                        <td class="pl5 p-r5 text-center">x</td>
                                        <td class="pl5 p-r5">
                                            <span>{{ $orderProduct->qty }}</span>
                                        </td>
                                        <td class="pl5 text-end">{{ format_price($orderProduct->price * $orderProduct->qty) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pd-all-20 p-none-t">
                        <div class="flexbox-grid-default block-rps-768">
                            <div class="flexbox-auto-right p-r5">

                            </div>
                            <div class="flexbox-auto-right pl5">
                                <div class="table-wrap">
                                    <table class="table-normal table-none-border table-color-gray-text">
                                        <tbody>
                                        <tr>
                                            <td class="text-end color-subtext">{{ trans('plugins/ecommerce::order.sub_amount') }}</td>
                                            <td class="text-end pl10">
                                                <span>{{ format_price($order->sub_total) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end color-subtext mt10">
                                                <p class="mb0">{{ trans('plugins/ecommerce::order.discount') }}</p>
                                                @if ($order->coupon_code)
                                                    <p class="mb0">{!! trans('plugins/ecommerce::order.coupon_code', ['code' => Html::tag('strong', $order->coupon_code)->toHtml()])  !!}</p>
                                                @elseif ($order->discount_description)
                                                    <p class="mb0">{{ $order->discount_description }}</p>
                                                @endif
                                            </td>
                                            <td class="text-end p-none-b pl10">
                                                <p class="mb0">{{ format_price($order->discount_amount) }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end color-subtext mt10">
                                                <p class="mb0">{{ trans('plugins/ecommerce::order.shipping_fee') }}</p>
                                                <p class="mb0 font-size-12px">{{ $order->shipping_method_name }}</p>
                                                <p class="mb0 font-size-12px">{{ ecommerce_convert_weight($weight) }} {{ ecommerce_weight_unit(true) }}</p>
                                            </td>
                                            <td class="text-end p-none-t pl10">
                                                <p class="mb0">{{ format_price($order->shipping_amount) }}</p>
                                            </td>
                                        </tr>
                                        @if (EcommerceHelper::isTaxEnabled())
                                            <tr>
                                                <td class="text-end color-subtext mt10">
                                                    <p class="mb0">{{ trans('plugins/ecommerce::order.tax') }}</p>
                                                </td>
                                                <td class="text-end p-none-t pl10">
                                                    <p class="mb0">{{ format_price($order->tax_amount) }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end mt10">
                                                <p class="mb0 color-subtext">{{ trans('plugins/ecommerce::order.total_amount') }}</p>
                                                @if ($order->payment->id)
                                                    <p class="mb0  font-size-12px">{{ $order->payment->payment_channel->label() }}</p>
                                                @endif
                                            </td>
                                            <td class="text-end text-no-bold p-none-t pl10">
                                                <span>{{ format_price($order->amount) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom"></td>
                                            <td class="border-bottom"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end color-subtext">{{ trans('plugins/ecommerce::order.paid_amount') }}</td>
                                            <td class="text-end color-subtext pl10">
                                                <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->payment->amount : 0) }}</span>
                                            </td>
                                        </tr>
                                        @if ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::REFUNDED)
                                            <tr class="hidden">
                                                <td class="text-end color-subtext">{{ trans('plugins/ecommerce::order.refunded_amount') }}</td>
                                                <td class="text-end pl10">
                                                    <span>{{ format_price($order->payment->amount) }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="hidden">
                                            <td class="text-end color-subtext">{{ trans('plugins/ecommerce::order.amount_received') }}</td>
                                            <td class="text-end pl10">
                                                <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->amount : 0) }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                @if ($order->isInvoiceAvailable())
                                    <div class="text-end">
                                        <a href="{{ route('marketplace.vendor.orders.generate-invoice', $order->id) }}" class="btn btn-info">
                                            <i class="fa fa-download"></i> {{ trans('plugins/ecommerce::order.download_invoice') }}
                                        </a>
                                    </div>
                                @endif
                                <div class="pd-all-20">
                                    <form action="{{ route('marketplace.vendor.orders.edit', $order->id) }}">
                                        <label class="text-title-field">{{ trans('plugins/ecommerce::order.note') }}</label>
                                        <textarea class="ui-text-area textarea-auto-height" name="description" rows="3" placeholder="{{ trans('plugins/ecommerce::order.add_note') }}">{{ $order->description }}</textarea>
                                        <div class="mt10">
                                            <button type="button" class="btn btn-primary btn-update-order">{{ trans('plugins/ecommerce::order.save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pd-all-20 border-top-title-main">
                        <div class="flexbox-grid-default flexbox-align-items-center">
                            <div class="flexbox-auto-left">
                                <svg class="svg-next-icon svg-next-icon-size-20 @if ($order->is_confirmed) svg-next-icon-green @else svg-next-icon-gray @endif">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                </svg>
                            </div>
                            <div class="flexbox-auto-right ml15 mr15 text-upper">
                                @if ($order->is_confirmed)
                                    <span>{{ trans('plugins/ecommerce::order.order_was_confirmed') }}</span>
                                @else
                                    <span>{{ trans('plugins/ecommerce::order.confirm_order') }}</span>
                                @endif
                            </div>
                            @if (!$order->is_confirmed)
                                <div class="flexbox-auto-left">
                                    <form action="{{ route('marketplace.vendor.orders.confirm') }}">
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <button class="btn btn-primary btn-confirm-order">{{ trans('plugins/ecommerce::order.confirm') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
                        <div class="pd-all-20 border-top-title-main">
                            <div class="flexbox-grid-default flexbox-flex-wrap flexbox-align-items-center">
                                <div class="flexbox-auto-left">
                                    <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-error"></use>
                                    </svg>
                                </div>
                                <div class="flexbox-auto-content ml15 mr15 text-upper">
                                    <span>{{ trans('plugins/ecommerce::order.order_was_canceled') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="pd-all-20 border-top-title-main">
                        <div class="flexbox-grid-default flexbox-flex-wrap flexbox-align-items-center">
                            @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED && !$order->shipment->id)
                                <div class="flexbox-auto-left">
                                    <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                    </svg>
                                </div>
                                <div class="flexbox-auto-content ml15 mr15 text-upper">
                                    <span>{{ trans('plugins/ecommerce::order.all_products_are_not_delivered') }}</span>
                                </div>
                            @else
                                @if ($order->shipment->id)
                                    <div class="flexbox-auto-left">
                                        <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                        </svg>
                                    </div>
                                    <div class="flexbox-auto-content ml15 mr15 text-upper">
                                        <span>{{ trans('plugins/ecommerce::order.delivery') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if (!$order->shipment->id)
                        <div class="shipment-create-wrap hidden"></div>
                    @else
                        @include(MarketplaceHelper::viewPath('dashboard.orders.shipment-detail'), ['shipment' => $order->shipment])
                    @endif
                </div>
                <div class="mt20 mb20">
                    <div>
                        <div class="comment-log ws-nm">
                            <div class="comment-log-title">
                                <label class="bold-light m-xs-b hide-print">{{ trans('plugins/ecommerce::order.history') }}</label>
                            </div>
                            <div class="comment-log-timeline">
                                <div class="column-left-history ps-relative" id="order-history-wrapper">
                                    @foreach ($order->histories()->orderBy('id', 'DESC')->get() as $history)
                                        <div class="item-card">
                                            <div class="item-card-body clearfix">
                                                <div class="item comment-log-item comment-log-item-date ui-feed__timeline">
                                                    <div class="ui-feed__item ui-feed__item--message">
                                                        <span class="ui-feed__marker @if ($history->user_id) ui-feed__marker--user-action @endif"></span>
                                                        <div class="ui-feed__message">
                                                            <div class="timeline__message-container">
                                                                <div class="timeline__inner-message">
                                                                    @if (in_array($history->action, ['confirm_payment', 'refund']))
                                                                        <a href="#" class="text-no-bold show-timeline-dropdown hover-underline" data-target="#history-line-{{ $history->id }}">
                                                                            <span>{{ OrderHelper::processHistoryVariables($history) }}</span>
                                                                        </a>
                                                                    @else
                                                                        <span>{{ OrderHelper::processHistoryVariables($history) }}</span>
                                                                    @endif
                                                                </div>
                                                                <time class="timeline__time"><span>{{ $history->created_at }}</span></time>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($history->action == 'refund' && Arr::get($history->extras, 'amount', 0) > 0)
                                                        <div class="timeline-dropdown" id="history-line-{{ $history->id }}">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.order_number') }}</th>
                                                                    <td><a href="{{ route('marketplace.vendor.orders.edit', $order->id) }}" title="{{ get_order_code($order->id) }}">{{ get_order_code($order->id) }}</a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.description') }}</th>
                                                                    <td>{{ $history->description . ' ' . trans('plugins/ecommerce::order.from') . ' ' . $order->payment->payment_channel->label() }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.amount') }}</th>
                                                                    <td>{{ format_price(Arr::get($history->extras, 'amount', 0)) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.status') }}</th>
                                                                    <td>{{ trans('plugins/ecommerce::order.successfully') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.transaction_type') }}</th>
                                                                    <td>{{ trans('plugins/ecommerce::order.refund') }}</td>
                                                                </tr>
                                                                @if (trim($order->payment->user->getFullName()))
                                                                    <tr>
                                                                        <th>{{ trans('plugins/ecommerce::order.staff') }}</th>
                                                                        <td>{{ $order->payment->user->getFullName() ? $order->payment->user->getFullName() : trans('plugins/ecommerce::order.n_a') }}</td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.refund_date') }}</th>
                                                                    <td>{{ $history->created_at }}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                    @if ($history->action == 'confirm_payment' && $order->payment)
                                                        <div class="timeline-dropdown" id="history-line-{{ $history->id }}">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.order_number') }}</th>
                                                                    <td><a href="{{ route('marketplace.vendor.orders.edit', $order->id) }}" title="{{ get_order_code($order->id) }}">{{ get_order_code($order->id) }}</a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.description') }}</th>
                                                                    <td>{!! trans('plugins/ecommerce::order.mark_payment_as_confirmed', ['method' => $order->payment->payment_channel->label()]) !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.transaction_amount') }}</th>
                                                                    <td>{{ format_price($order->payment->amount) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.payment_gateway') }}</th>
                                                                    <td>{{ $order->payment->payment_channel->label() }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.status') }}</th>
                                                                    <td>{{ trans('plugins/ecommerce::order.successfully') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.transaction_type') }}</th>
                                                                    <td>{{ trans('plugins/ecommerce::order.confirm') }}</td>
                                                                </tr>
                                                                @if (trim($order->payment->user->getFullName()))
                                                                    <tr>
                                                                        <th>{{ trans('plugins/ecommerce::order.staff') }}</th>
                                                                        <td>{{ $order->payment->user->getFullName() ? $order->payment->user->getFullName() : trans('plugins/ecommerce::order.n_a') }}</td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <th>{{ trans('plugins/ecommerce::order.payment_date') }}</th>
                                                                    <td>{{ $history->created_at }}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                    @if ($history->action == 'send_order_confirmation_email')
                                                        <div class="ui-feed__item ui-feed__item--action">
                                                            <span class="ui-feed__spacer"></span>
                                                            <div class="timeline__action-group">
                                                                <a href="#" class="btn hide-print timeline__action-button hover-underline btn-trigger-resend-order-confirmation-modal" data-action="{{ route('marketplace.vendor.orders.send-order-confirmation-email', $history->order_id) }}">{{ trans('plugins/ecommerce::order.resend') }}</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="flexbox-layout-section-secondary mt20">
            <div class="ui-layout__item">
                <div class="wrapper-content mb20">
                    <div class="next-card-section p-none-b">
                        <div class="flexbox-grid-default flexbox-align-items-center">
                            <div class="flexbox-auto-content-left">
                                <label class="title-product-main text-no-bold">{{ trans('plugins/ecommerce::order.customer_label') }}</label>
                            </div>
                            <div class="flexbox-auto-left">
                                <img class="width-30-px radius-cycle" width="40" src="{{ $order->user->id ? $order->user->avatar_url : $order->address->avatar_url }}" alt="{{ $order->address->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="next-card-section border-none-t">
                        <div class="mb5">
                            <strong class="text-capitalize">{{ $order->user->name ?  : $order->address->name }}</strong>
                        </div>
                        @if ($order->user->id)
                            <div><i class="fas fa-inbox mr5"></i><span>{{ $order->user->orders()->count() }}</span> {{ trans('plugins/ecommerce::order.orders') }}</div>
                        @endif
                        <ul class="ws-nm text-infor-subdued">
                            <li class="overflow-ellipsis"><a class="hover-underline" href="mailto:{{ $order->user->email ?: $order->address->email }}">{{ $order->user->email ?: $order->address->email }}</a></li>
                            @if ($order->user->id)
                                <li><div>{{ trans('plugins/ecommerce::order.have_an_account_already') }}</div></li>
                            @else
                                <li><div>{{ trans('plugins/ecommerce::order.dont_have_an_account_yet') }}</div></li>
                            @endif
                        </ul>
                    </div>
                    <div class="next-card-section">
                        <div class="flexbox-grid-default flexbox-align-items-center">
                            <div class="flexbox-auto-content-left">
                                <label class="title-text-second"><strong>{{ trans('plugins/ecommerce::order.shipping_address') }}</strong></label>
                            </div>
                            @if ($order->status != \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
                                <div class="flexbox-auto-content-right text-end">
                                    <a href="#" class="btn-trigger-update-shipping-address">
                                    <span data-placement="top" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('plugins/ecommerce::order.update_address') }}">
                                        <svg class="svg-next-icon svg-next-icon-size-12">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-edit"></use>
                                        </svg>
                                    </span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div>
                            <ul class="ws-nm text-infor-subdued shipping-address-info">
                                @include('plugins/ecommerce::orders.shipping-address.detail', ['address' => $order->address])
                            </ul>
                        </div>
                    </div>
                </div>

                @if (!in_array($order->status, [\Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED, \Botble\Ecommerce\Enums\OrderStatusEnum::COMPLETED]))
                    <div class="wrapper-content bg-gray-white mb20">
                        <div class="pd-all-20">
                            <a href="#" class="btn btn-secondary btn-trigger-cancel-order" data-target="{{ route('marketplace.vendor.orders.cancel', $order->id) }}">{{ trans('plugins/ecommerce::order.cancel') }}</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($order->status != \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
        {!! Form::modalAction('resend-order-confirmation-email-modal', trans('plugins/ecommerce::order.resend_order_confirmation'), 'info', trans('plugins/ecommerce::order.resend_order_confirmation_description', ['email' => $order->user->id ? $order->user->email : $order->address->email]), 'confirm-resend-confirmation-email-button', trans('plugins/ecommerce::order.send')) !!}
        {!! Form::modalAction('update-shipping-address-modal', trans('plugins/ecommerce::order.update_address'), 'info', view('plugins/ecommerce::orders.shipping-address.form', ['address' => $order->address, 'orderId' => $order->id, 'url' => route('marketplace.vendor.orders.update-shipping-address', $order->address->id ?? 0)])->render(), 'confirm-update-shipping-address-button', trans('plugins/ecommerce::order.update'), 'modal-md') !!}
        {!! Form::modalAction('cancel-order-modal', trans('plugins/ecommerce::order.cancel_order_confirmation'), 'info', trans('plugins/ecommerce::order.cancel_order_confirmation_description'), 'confirm-cancel-order-button', trans('plugins/ecommerce::order.cancel_order')) !!}
    @endif
@stop
