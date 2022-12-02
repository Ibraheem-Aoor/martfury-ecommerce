<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive ppq-table">
                <tr>
                    <th>{{ trans('plugins/ecommerce::products.form.quantity') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.price_per_qty') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.total') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.price_sale') }} %</th>
                    <th> <button type="button"  class="btn btn-sm btn-primary add-price-per-qty"><i
                                class="fa fa-plus"></i></button></th>
                </tr>
                @php
                    $i = 0;
                @endphp
                @if (isset($product) && $product->pricePerQty != null)
                    @foreach ($product->pricePerQty as $ppq)
                        <tr>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][quantity]"
                                    class="form-control" value="{{ $ppq->quantity }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][sale_price]"
                                    class="form-control sale-price" value="{{ $ppq->sale_price }}"
                                    onkeyup="calcSaleRate($(this));">
                            </td>
                            <td>
                                <input type="number" readonly class="form-control sale-price"
                                    value="{{ $ppq->sale_price * $ppq->quantity }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i++ }}][sale_rate]"
                                    class="form-control" value="{{ $ppq->sale_rate }}" readonly>
                            </td>
                            <td class="d-flex">
                                <button type="button" style="font-size: 8px !important;" class="btn btn-sm btn-primary add-price-per-qty"><i
                                        class="fa fa-plus"></i></button>
                                <button type="button" style="font-size: 8px !important;" class="btn btn-sm btn-danger remove-price-per-qty"><i
                                        class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @elseif(old('ppq'))
                    @foreach (old('ppq') as $ppq)
                        <tr>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][quantity]"
                                    class="form-control" value="{{ $ppq['quantity'] }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][sale_price]"
                                    class="form-control sale-price" value="{{ $ppq['sale_price'] }}"
                                    onkeyup="calcSaleRate($(this));">
                            </td>
                            <td>
                                <input type="number" readonly class="form-control sale-price"
                                    value="{{ $ppq['sale_price'] * $ppq['quantity'] }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i++ }}][sale_rate]"
                                    class="form-control" value="{{ $ppq['sale_rate'] }}" readonly>
                            </td>
                            <td class="d-flex">
                                <button type="button" style="font-size: 8px !important;" class="btn btn-sm btn-primary add-price-per-qty"><i
                                        class="fa fa-plus"></i></button>

                                <button type="button" style="font-size: 8px !important;" class="btn btn-sm btn-danger remove-price-per-qty"><i
                                        class="fa fa-trash"></i></button>

                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <input required type="number" name="ppq[1][quantity]" class="form-control">
                        </td>
                        <td>
                            <input required type="number" name="ppq[1][sale_price]" class="form-control sale-price"
                                onkeyup="calcSaleRate($(this));">
                        </td>
                        <td>
                            <input readonly type="number" class="form-control sale-price">
                        </td>
                        <td>
                            <input required type="number" name="ppq[1][sale_rate]" readonly placeholder="%"
                                class="form-control">
                        </td>
                        <td class="d-flex">
                            <button type="button" style="font-size: 8px !important;" class="btn btn-sm btn-primary add-price-per-qty"><i
                                    class="fa fa-plus"></i></button>
                            <button type="button" style="font-size: 8px !important;" class="btn btn-sm btn-danger remove-price-per-qty"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
</div>

@section('javascript')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/price-per-qty.js') }}"></script>
@endsection
