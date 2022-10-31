script<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.weight') }}
                    ({{ ecommerce_width_height_unit() }})</label>
                <input type="number" name="attr_weight" class="form-control"
                    value="{{ old('attr_weight', isset($product) ? $product->attr_weight : null) }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.height') }}
                    ({{ ecommerce_width_height_unit() }})</label>
                <input type="number" name="attr_height" class="form-control"
                    value="{{ old('attr_height', isset($product) ? $product->attr_height : null) }}">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.wide') }}
                    ({{ ecommerce_width_height_unit() }})</label>
                <input type="number" name="attr_width" class="form-control"
                    value="{{ old('attr_width', isset($product) ? $product->attr_width : null) }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.length') }}
                    ({{ ecommerce_width_height_unit() }})</label>
                <input type="number" name="attr_length" class="form-control"
                    value="{{ old('attr_length', isset($product) ? $product->attr_length : null) }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Product Country') }}
                </label>
                <select name="product_country" class="form-control">
                    <option value="">--SELECT ONE--</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}" @if (old('product_country', isset($product) ? $product->product_country : null)) selected @endif>
                            {{ $country }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Product Language') }}</label>
                <select name="packaging_language" class="form-control">
                    @foreach ($languages as $language)
                        <option value="{{ $language[2] }}" @if ($language[2] == $product->packaging_language) selected @endif>
                            {{ $language[2] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Product Material') }}</label>
                <input type="text" name="product_meterial" class="form-control"
                    value="{{ old('product_meterial', isset($product) ? $product->product_meterial : null) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Number of pieces') }}</label>
                <input type="text" name="peice_count" class="form-control"
                    value="{{ old('peice_count', isset($product) ? $product->peice_count : null) }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label
                    class="text-title-field required text-center">{{ trans('plugins/ecommerce::products.form.Delivery time') }}</label>
                <select type="number" name="delivery_time" class="form-control text-center"
                    value="{{ old('delivery_time', isset($product) ? $product->delivery_time : null) }}">
                    <option value="">--SELECT--</option>
                    <option value="24 Hour" @if (old('delivery_time') == '24 Hour' || (isset($product) && $product->delivery_time == '24 Hour')) selected @endif>
                        {{ trans('plugins/ecommerce::products.form.24 Hour') }}</option>
                    <option value="2 Days" @if (old('delivery_time') == '2 Days' || (isset($product) && $product->delivery_time == '2 Days')) selected @endif>
                        {{ trans('plugins/ecommerce::products.form.2 Days') }}</option>
                    <option value="5 Days" @if (old('delivery_time') == '5 Days' || (isset($product) && $product->delivery_time == '5 Days')) selected @endif>
                        {{ trans('plugins/ecommerce::products.form.5 Days') }}</option>
                    <option value="1 Week" @if (old('delivery_time') == '1 Week' || (isset($product) && $product->delivery_time == '1 Week')) selected @endif>
                        {{ trans('plugins/ecommerce::products.form.1 Week') }}</option>
                    <option value="2 Weeks" @if (old('delivery_time') == '2 Weeks' || (isset($product) && $product->delivery_time == '2 Weeks')) selected @endif>
                        {{ trans('plugins/ecommerce::products.form.2 Weeks') }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Is Guaranteed') }}</label>
                <input type="radio" name="is_guaranteed" id="is_guaranteed_true" value="1"
                    @if ($product?->is_guaranteed == 1) checked @endif>
                {{ trans('plugins/ecommerce::products.form.yes') }}&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="is_guaranteed" id="is_guaranteed_false" value="0"
                    @if ($product?->is_guaranteed == 0) checked @endif>
                {{ trans('plugins/ecommerce::products.form.no') }}
            </div>
            <div class="fom-group" id="guanrtee-details-div">
                <label
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Guarantee Details') }}</label>
                <textarea class="form-control editor-ckeditor" rows="2" name="guarantee" id="guarntee-details"
                    with-short-code="true" aria-required="true">{{ isset($product) ? $product->guarantee : old('guarantee', null) }}</textarea>
            </div>
        </div>


    </div>
</div>



{{--

    <div class="form-group" id="refund-details-div">
                <label for="refund-details"
                    class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Weig') }}</label>
                <textarea class="form-control form-control editor-ckeditor" rows="2" with-short-code="true"
                    id="refund-details" name="refund_details" cols="40" aria-required="true"></textarea>
            </div>


    --}}

<script>
    var is_guaranteed = "{{ $product?->is_guaranteed == 1 || old('is_guaranteed') == 1 ? true : false }}";
</script>
