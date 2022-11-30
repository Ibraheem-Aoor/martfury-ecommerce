<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive">
                <tr>
                    <th>{{ trans('plugins/ecommerce::products.form.price') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.quantity') }}</th>
                    <th>{{ trans('plugins/ecommerce::products.form.price_sale') }} %</th>
                </tr>
                <tr>
                    <td>
                        <input type="number" name="ppq[1]['sale_price']" class="form-control sale-price">
                    </td>
                    <td>
                        <input type="number" name="ppq[1]['sale_quantity']" class="form-control">
                    </td>
                    <td>
                        <input type="number" name="ppq[1]['sale_rate']" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary mb-1 mt-3 add-price-per-qty"><i
                                class="fa fa-plus"></i></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
