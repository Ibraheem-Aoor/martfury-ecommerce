@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
<div class="row">
    <div class="col-md-3 right-sidebar">
        <div class="widget meta-boxes">
            <div class="widget-title">
                <h4><label for="status" class="control-label" aria-required="true">{{ trans('plugins/marketplace::revenue.store_information') }}</label></h4>
            </div>
            <div class="widget-body">
                <div class="form-group mb-3">
                    <div class="border-bottom py-2">
                        <div class="text-center">
                            <div class="text-center">
                                <img src="{{ RvMedia::getImageUrl($store->logo, 'thumb', false, RvMedia::getDefaultImage()) }}" width="120" class="mb-2" style="border-radius: 50%" alt="avatar" />
                            </div>
                            <div class="text-center">
                                <strong>
                                    <a href="{{ $store->url }}" target="_blank">{{ $store->name }} <i class="fas fa-external-link-alt"></i></a>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="py-2">
                        <span>{{ trans('plugins/marketplace::revenue.vendor_name') }}:</span>
                        <strong><a href="{{ route('customers.edit', $customer->id) }}" target="_blank">{{ $customer->name }} <i class="fas fa-external-link-alt"></i></a></strong>
                    </div>
                    <div class="py-2">
                        <span>{{ trans('plugins/marketplace::revenue.balance') }}:</span>
                        <strong class="vendor-balance">{{ format_price($customer->balance) }} <a href="#" data-bs-toggle="modal" data-bs-target="#update-balance-modal"><i class="fa fa-edit"></i></a> </strong>
                    </div>
                    <div>
                        <span>{{ trans('plugins/marketplace::revenue.products') }}:</span>
                        <strong>{{ number_format($store->products()->count()) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="widget meta-boxes">
            <div class="widget-title">
                <h4><label for="status" class="control-label" aria-required="true">{{ trans('plugins/marketplace::revenue.statements') }}</label></h4>
                <a href="#" class="me-2 d-inline-block float-end" data-bs-toggle="modal" data-bs-target="#update-balance-modal">
                    <small><i class="fa fa-edit"></i> {{ trans('plugins/marketplace::revenue.update_balance') }}</small>
                </a>
            </div>
            <div class="widget-body">
                {!! $table->renderTable() !!}
            </div>
        </div>
    </div>

    {!! Form::modalAction('update-balance-modal',
        trans('plugins/marketplace::revenue.update_balance_title'),
        'info',
        view('plugins/marketplace::stores.balance-form', compact('store', 'customer'))->render(),
        'confirm-update-amount-button',
        trans('core/base::tables.submit'),
        'modal-md') !!}
</div>
@stop
