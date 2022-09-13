@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
<div class="row">
    <div class="col-md-3 right-sidebar">
        <div class="widget meta-boxes">
            <div class="widget-title">
                <h4><label for="status" class="control-label" aria-required="true">{{ trans('plugins/marketplace::store.information') }}</label></h4>
            </div>
            <div class="widget-body">
                <div class="form-group mb-3">
                    <div class="border-bottom py-2">
                        <div class="text-center">
                            <div class="text-center">
                                <img src="{{ RvMedia::getImageUrl($vendor->store->logo, 'thumb', false, RvMedia::getDefaultImage()) }}" width="120" class="mb-2" style="border-radius: 50%" alt="avatar" />
                            </div>
                            <div class="text-center">
                                <strong>
                                    <a href="{{ route('marketplace.store.edit', $vendor->store->id) }}" target="_blank">{{ $vendor->store->name }} <i class="fas fa-external-link-alt"></i></a>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="py-2">
                        <span>{{ trans('plugins/marketplace::store.store_phone') }}:</span>
                        <strong>{{ $vendor->store->phone ?: 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="note note-warning">
            <p>{!! clean(trans('plugins/marketplace::unverified-vendor.vendor_approval_notification', [
            'vendor'       => Html::link(route('marketplace.store.edit', $vendor->store->id), $vendor->store->name, ['target' => '_blank']),
            'approve_link' => Html::link(route('marketplace.unverified-vendors.approve-vendor', $vendor->id), trans('plugins/marketplace::store.approve_here'), ['class' => 'approve-vendor-for-selling-button']),
        ])) !!}</p>
        </div>

        <div class="widget meta-boxes">
            <div class="widget-title">
                <h4><label for="status" class="control-label" aria-required="true">{{ trans('plugins/marketplace::store.vendor_information') }}</label></h4>
            </div>
            <div class="widget-body">
                <div class="py-2">
                    <span>{{ trans('plugins/marketplace::store.vendor_name') }}:</span>
                    <strong><a href="{{ route('customers.edit', $vendor->id) }}" target="_blank">{{ $vendor->name }} <i class="fas fa-external-link-alt"></i></a></strong>
                </div>
                <div class="py-2">
                    <span>{{ trans('plugins/marketplace::unverified-vendor.forms.email') }}:</span>
                    <strong>{{ $vendor->email }}</strong>
                </div>
                <div class="py-2">
                    <span>{{ trans('plugins/marketplace::unverified-vendor.forms.vendor_phone') }}:</span>
                    <strong>{{ $vendor->phone ?: 'N/A' }}</strong>
                </div>
                <div class="py-2">
                    <span>{{ trans('plugins/marketplace::unverified-vendor.forms.registered_at') }}:</span>
                    <strong>{{ $vendor->created_at }}</strong>
                </div>
            </div>
        </div>
    </div>

    {!! Form::modalAction('approve-vendor-for-selling-modal', trans('plugins/marketplace::unverified-vendor.approve_vendor_confirmation'), 'warning', trans('plugins/marketplace::unverified-vendor.approve_vendor_confirmation_description', ['vendor' => $vendor->name]), 'confirm-approve-vendor-for-selling-button', trans('plugins/marketplace::unverified-vendor.approve')) !!}
</div>
@stop
