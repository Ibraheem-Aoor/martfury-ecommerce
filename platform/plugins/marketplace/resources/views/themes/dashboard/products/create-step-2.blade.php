@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form data-route="{{}}"
            id="ean_code_check" action="{{ route('products.ean_check') }}">
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
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Choose Product Category</label>
                        <select class="form-control" required name="parent_id" id="parent_id"
                            data-route="{{ route('marketplace.vendor.products.get_children_categories') }}">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">&nbsp;</label>
                        <select class="form-control" name="sub_1_id" id="sub_1_id">
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">&nbsp;</label>
                        <select class="form-control" name="sub_2_id" id="sub_2_id">
                        </select>
                    </div>
                </div>

            </div>
            <div class="col-sm-12">
                <div class="form-group text-center">
                    <button type="button" onclick="$('#ean_code_check').submit();"
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
