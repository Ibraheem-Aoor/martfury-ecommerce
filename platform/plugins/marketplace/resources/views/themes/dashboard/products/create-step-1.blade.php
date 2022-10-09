@extends('plugins/marketplace::themes.dashboard.layouts.master')
@section('content')
    <div class="container">
        <form id="step_1_form" action="{{ route('marketplace.vendor.products.post_create_step_1') }}">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="" class="form-text">{{ __('Enter Product Name') }}:</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" value="{{ $name ?? null }}">
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
                                <option value="{{ $category->id }}" @if (isset($product_categories) &&  $product_categories[0] == $category->id) selected @endif>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">&nbsp;</label>
                        <select class="form-control" name="sub_1_id" id="sub_1_id">
                            @if ($sub_1_category)
                                <option value="{{ $sub_1_category->id }}">{{ $sub_1_category->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">&nbsp;</label>
                        <select class="form-control" name="sub_2_id" id="sub_2_id">
                            @if ($sub_2_category)
                                <option value="{{ $sub_2_category->id }}">{{ $sub_2_category->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>

            </div>
            <div class="col-sm-12">
                <div class="form-group text-center">
                    <button type="button" onclick="$('#step_1_form').submit();"
                        class="btn btn-outline-success">{{ __('Next') }} <i class="fa fa-arrow-right"></i></button>
                </div>
            </div>

        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_1.js') }}"></script>
    <script>
        @if ($sub_1_category)
            sub_1_id_select.show();
        @endif
        @if ($sub_2_category)
            sub_2_id_select.show();
        @endif
    </script>
@endpush
