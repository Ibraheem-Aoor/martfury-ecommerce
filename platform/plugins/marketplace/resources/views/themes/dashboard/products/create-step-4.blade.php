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
                        <input required type="number" name="weight" class="form-control" value="{{$weight ?? null}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.height') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="height" class="form-control" value="{{$height ?? null}}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.wide') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="wide" class="form-control" value="{{$wide ?? null}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label
                            class="text-title-field required">{{ trans('plugins/ecommerce::products.form.shipping.length') }}
                            ({{ ecommerce_width_height_unit() }})</label>
                        <input required type="number" name="length" class="form-control" value="{{$length ?? null}}">
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
                            with-short-code="true" aria-required="true">{{ $guarantee ?? null }}</textarea>
                    </div>
                </div>
            </div>


            <div class="row mt-5">
                <div class="col-sm-3"></div>
                <div class="col-sm-3 text-left">
                    <div class="form-group">
                        <a href="{{ route('marketplace.vendor.products.back_to_step', 3) }}"
                            class="btn btn-outline-warning p-2"><i class="fa fa-arrow-left"></i>
                            {{ __('Back') }}</a>
                    </div>
                </div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3 text-right">
                    <div class="form-group">
                        <button type="button" onclick="$('#step_4_form').submit();"
                            class="btn btn-outline-success p-2">{{ __('Next') }} <i
                                class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>


        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_4.js') }}"></script>
    <script>
        @if(isset($is_guaranteed) && ($is_guaranteed == 1))
            $('#is_guaranteed_true').click();
        @endif
    </script>
@endpush
