<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive ppq-table">
                <tr>
                    <th>{{ trans('plugins/ecommerce::products.form.price') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.quantity') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.price_sale') }} %</th>
                    <th> <button type="button" class="btn btn-sm btn-primary add-price-per-qty"><i
                                class="fa fa-plus"></i></button></th>
                </tr>
                @php
                    $i = 0;
                @endphp
                @if (isset($product) && $product->productPricePerQuantity != null)
                    @foreach ($product->pricePerQty as $ppq)
                        <tr>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][sale_price]"
                                    class="form-control sale-price" value="{{ $ppq->sale_price }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][quantity]"
                                    class="form-control" value="{{ $ppq->quantity }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i++ }}][sale_rate]"
                                    class="form-control" value="{{ $ppq->sale_rate }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary add-price-per-qty"><i
                                        class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-sm btn-danger remove-price-per-qty"><i
                                        class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @elseif(old('ppq'))
                    @foreach (old('ppq') as $ppq)
                        <tr>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][sale_price]"
                                    class="form-control sale-price" value="{{ $ppq['sale_price'] }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i }}][quantity]"
                                    class="form-control" value="{{ $ppq['quantity'] }}">
                            </td>
                            <td>
                                <input required type="number" name="ppq[{{ $i++ }}][sale_rate]"
                                    class="form-control" value="{{ $ppq['sale_rate'] }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary add-price-per-qty"><i
                                        class="fa fa-plus"></i></button>

                                <button type="button" class="btn btn-sm btn-danger remove-price-per-qty"><i
                                        class="fa fa-trash"></i></button>

                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <input required type="number" name="ppq[1][sale_price]" class="form-control sale-price">
                        </td>
                        <td>
                            <input required type="number" name="ppq[1][quantity]" class="form-control">
                        </td>
                        <td>
                            <input required type="number" name="ppq[1][sale_rate]" placeholder="%"
                                class="form-control">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary add-price-per-qty"><i
                                    class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-danger remove-price-per-qty"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
</div>
