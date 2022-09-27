<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Can Be Refunded?') }}</label>
                <input type="radio" name="is_refunded" id="is_refunded_true" value="1"
                    class="form-input">{{ trans('plugins/ecommerce::products.form.yes') }}&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="is_refunded" id="is_refunded_false" value="0"
                    class="form-input">{{ trans('plugins/ecommerce::products.form.no') }}
            </div>
            <div class="form-group" id="refund-details-div">
                <label for="refund-details"
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Refund Details') }}</label>
                <textarea class="form-control form-control editor-ckeditor" rows="2" with-short-code="true"
                    id="refund-details" name="refund_details" cols="40" aria-required="true">{{isset($product) ? $product->refund_details : old('refund_details' , null)}}</textarea>
            </div>
        </div>
    </div>
</div>
