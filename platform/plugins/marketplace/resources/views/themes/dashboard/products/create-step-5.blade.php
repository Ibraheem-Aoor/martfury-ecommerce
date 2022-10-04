@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form id="step_4_form" action="{{ route('marketplace.vendor.products.post_create_step_4') }}">
            <div class="row">
                <div class="form-group">
                    @include('plugins/ecommerce::products.partials.add-product-attributes')
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
