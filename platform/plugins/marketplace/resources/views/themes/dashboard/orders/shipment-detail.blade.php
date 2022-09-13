<div class="shipment-info-panel hide-print">
    <div class="shipment-info-header">
        <h4>{{ get_shipment_code($shipment->id) }}</h4>
        <span class="label carrier-status carrier-status-{{ $shipment->status }}">{{ $shipment->status->label() }}</span>
    </div>

    <div class="pd-all-20 pt10">
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.shipping_method') }}: <span><i>{{ $shipment->order->shipping_method_name }}</i></span></span>
            </div>
            <div class="flexbox-grid-form-item rps-no-pd-none-r ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.weight_unit', ['unit' => ecommerce_weight_unit()]) }}:</span> <span><i>{{ $shipment->weight }} {{ ecommerce_weight_unit() }}</i></span>
            </div>
        </div>
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.shipping_status') }}:</span> <strong><i>{{ $shipment->status->label() }}</i></strong>
            </div>
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.updated_at') }}:</span> <span><i>{{ $shipment->updated_at }}</i></span>
            </div>
        </div>
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm rps-no-pd-none-r">
                <span>{{ trans('plugins/ecommerce::shipping.cod_amount') }}:</span>
                <span><i>{{ format_price($shipment->cod_amount) }}</i></span>
            </div>
            @if ($shipment->note)
                <div class="flexbox-grid-form-item ws-nm rps-no-pd-none-r">
                    <span>{{ trans('plugins/ecommerce::shipping.delivery_note') }}:</span>
                    <span><i>{{ $shipment->note }}</i></span>
                </div>
            @endif
        </div>
    </div>
</div>
