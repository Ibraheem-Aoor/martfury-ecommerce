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
                        <input type="number" name="attr_weight" class="form-control" value="{{ $attr_weight ?? null }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.height') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_height" class="form-control" value="{{ $attr_height ?? null }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.wide') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_width" class="form-control" value="{{ $attr_width ?? null }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.length') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input type="number" name="attr_length" class="form-control" value="{{ $attr_length ?? null }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.product_meterial') }}
                            &nbsp;
                            <i class="fa fa-question" style="display:inline !important;"
                                title="{{ trans('plugins/ecommerce::products.form.product_meterial_placeholder') }}"></i>
                        </label>
                        <input type="text" class="form-control" name="product_meterial"
                            value="{{ $product_meterial ?? null }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.peice_count') }}
                        </label>
                        <input type="number" class="form-control" name="peice_count" value="{{ $peice_count ?? null }}">
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
                                    <option value="{{ $country }}" @if (isset($product_country) && $product_country == $country) selected @endif>
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
                                    <option value="{{ $language[2] }}" @if (isset($product_country) && $language[2] == $product_country) selected @endif>
                                        {{ $language[2] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row mt-5">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3 text-left">
                        <div class="form-group">
                            <a href="{{ route('marketplace.vendor.products.back_to_step', 2) }}"
                                class="btn btn-outline-warning p-2"><i class="fa fa-arrow-left"></i>
                                {{ __('Back') }}</a>
                        </div>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3 text-right">
                        <div class="form-group">
                            <button type="button" onclick="$('#step_3_form').submit();"
                                class="btn btn-outline-success p-2">{{ __('Next') }} <i
                                    class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>

        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_3.js') }}"></script>
@endpush
