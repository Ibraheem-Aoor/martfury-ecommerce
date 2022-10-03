@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form
            id="step_1_form" action="{{ route('marketplace.vendor.products.post_create_step_1') }}">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="" class="form-text">{{ __('Enter Product Name') }}:</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="name">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-sm-12">
                <div class="form-group text-center">
                    <button type="button" onclick="$('#step_1_form').submit();"
                        class="btn btn-outline-success">{{ __('Next') }}</button>
                </div>
            </div>

        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_1.js') }}"></script>
    {{-- Start Ean Code Script --}}
    <script>
        $('#ean_code_check').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: "product-ean-check-vendor",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status && response.is_unique) {
                        location.href = response.route;
                    } else {
                        toastr.success('Created Successfully');
                        location.href = response.route;
                    }
                },
                error: function(response) {
                    $.each(response.responseJSON.errors, function(item, key) {
                        toastr.error(key);
                    });
                    form.reset();
                },
            });
        });
    </script>
    {{-- End Ean Code Script --}}
@endpush
