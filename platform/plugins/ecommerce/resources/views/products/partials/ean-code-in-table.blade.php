@if (Auth::user()->hasPermission('products.edit'))
    <a data-type="text" data-pk="{{ $item->id }}" style="font-size: 12px !important";
        data-url="{{ route('products.update-price-quanttiy-in-table', $flag) }}" data-value="{{ $item->ean_code }}"
        data-title="{{ trans('core/base::tables.ean') }}" class="editable" href="#">
        {{ $item->ean_code }}
    </a>
@else
    {{ $item->ean_code }}
@endif
<input type="hidden" name="ean_code" value="{{ $item->ean_code }}">
