@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form id="step_3_form" action="{{ route('marketplace.vendor.products.post_create_step_3') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.weight') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_weight" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.height') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_height" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.wide') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_width" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.length') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_length" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.product_meterial') }}
                        </label>
                        <input type="text" class="form-control" name="product_meterial">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.peice_count') }}
                        </label>
                        <input type="number" class="form-control" name="peice_count">
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
                                    <option>
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
                                    <option value="{{ $language[2] }}">{{ $language[2] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group text-center">
                        <button type="button" onclick="$('#step_3_form').submit();"
                            class="btn btn-outline-success">{{ __('Next') }}</button>
                    </div>
                </div>

        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_3.js') }}"></script>
@endpush
