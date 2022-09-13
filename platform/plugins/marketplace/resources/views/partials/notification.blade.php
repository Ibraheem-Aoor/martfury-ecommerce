@if (!$isApproved)
    <div class="note note-warning approve-product-warning">
        <p>{!! clean(trans('plugins/marketplace::store.product_approval_notification', [
            'vendor'       => Html::link($product->createdBy->store->url, $product->createdBy->store->name, ['target' => '_blank']),
            'approve_link' => Html::link(route('products.approve-product', $product->id), trans('plugins/marketplace::store.approve_here'), ['class' => 'approve-product-for-selling-button']),
        ])) !!}</p>
    </div>
@else
    <div class="note note-info approved-product-info">
        <p>{!! clean(trans('plugins/marketplace::store.product_approved_notification', [
            'vendor' => Html::link($product->createdBy->store->url, $product->createdBy->store->name, ['target' => '_blank']),
            'user'   => $product->approvedBy->name,
        ])) !!}</p>
    </div>
@endif

@push('footer')
    @if (!$isApproved)
        {!! Form::modalAction('approve-product-for-selling-modal', trans('plugins/marketplace::store.approve_product_confirmation'), 'warning', trans('plugins/marketplace::store.approve_product_confirmation_description', ['vendor' => Html::link($product->createdBy->store->url, $product->createdBy->store->name, ['target' => '_blank'])]), 'confirm-approve-product-for-selling-button', trans('plugins/marketplace::store.approve')) !!}
    @endif
@endpush
