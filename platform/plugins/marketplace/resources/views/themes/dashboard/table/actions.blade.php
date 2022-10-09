<div class="table-actions">
    @if (!empty($edit))
        <a href="{{ route($edit, $item->id) }}" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="tooltip"
            data-bs-original-title="{{ trans('core/base::tables.edit') }}"><i class="fa fa-edit"></i></a>
    @endif

    @if (!empty($delete))
        <a href="#" class="btn btn-icon btn-sm btn-danger deleteDialog"
            data-section="{{ route($delete, $item->id) }}" role="button" data-bs-toggle="tooltip"
            data-bs-original-title="{{ trans('core/base::tables.delete_entry') }}">
            <i class="fa fa-trash"></i>
        </a>
    @endif
    @if(Request::segment(2) == 'products')
    <a class="btn btn-icon btn-sm btn-info editQuantity" data-href="{{route('marketplace.vendor.products.change_quantity')}}" data-id="{{$item->id}}" data-quantity="{{$item->quantity}}" title="{{trans('core/base::tables.edit_quantity')}}">
        <i class="fa fa-cubes" style="color:black;font-size:1.6rem;"></i>
    </a>
    @endif
</div>
