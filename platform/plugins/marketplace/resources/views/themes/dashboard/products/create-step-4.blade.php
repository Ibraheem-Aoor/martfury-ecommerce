@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form id="step_4_form" action="{{ route('marketplace.vendor.products.post_create_step_4') }}">
            <div class="row">
                <label class="text-title-">{{ trans('plugins/ecommerce::products.form.shipping.title') }}
            </div>
            <div class="row">
                </label>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.weight') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="weight" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.height') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="height" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.wide') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="wide" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.length') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="length" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-12">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.Is Guaranteed') }}</label>
                        <input type="radio" name="is_guaranteed" id="is_guaranteed_true" value="1">
                        {{ trans('plugins/ecommerce::products.form.yes') }}&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="is_guaranteed" id="is_guaranteed_false" value="0">
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
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group text-center">
                        <button type="button" onclick="$('#step_4_form').submit();"
                            class="btn btn-outline-success">{{ __('Next') }}</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_4.js') }}"></script>
@endpush
