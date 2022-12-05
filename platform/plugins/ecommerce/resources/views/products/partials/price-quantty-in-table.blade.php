@if (Auth::user()->hasPermission('products.edit'))
    <a data-type="text" data-pk="{{ $item->id }}" style="font-size: 12px !important";
        data-url="{{ route('products.update-price-quanttiy-in-table', $flag) }}"
        data-value="{{ $flag == 'p' ? ((int) $item->sale_price != 0 ? $item->sale_price : $item->price) ?? 0 : $item->quantity ?? 0 }}"
        data-title="{{ $flag == 'p' ? trans('core/base::tables.price') : trans('core/base::tables.quantity') }}"
        class="editable" href="#">
        {{ $flag == 'p' ? ((int) $item->sale_price != 0 ? $item->sale_price : $item->price) ?? 0 : $item->quantity ?? 0 }}
    </a>
    @if ($flag == 'p')
        <span style="font-size:13px !important;">{{ get_application_currency()->symbol }} </span>
    @endif
@else
    {{ $flag == 'p' ? ((int) $item->sale_price != 0 ? $item->sale_price : $item->price) ?? 0 : $item->quantity ?? 0 }}
@endif
@if ((int) $item->sale_price != 0 && $flag == 'p')
    <br>
    <del class="text-danger"> {{ $item->price }}
        @if ($flag == 'p')
            <span style="font-size:13px !important;">{{ get_application_currency()->symbol }} </span>
        @endif
    </del>
@endif
