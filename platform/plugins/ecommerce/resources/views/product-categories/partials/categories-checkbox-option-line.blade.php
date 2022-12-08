<?php
/**
 * @var string $value
 */
if (isset($value)) {
    $selected_categories = @$value;
}
?>

{{-- child categoris route --}}
<input name="child_categories_route" hidden value="{{ route('marketplace.vendor.products.get_children_categories') }}">

@if ($categories)
    <select class="form-control" required name="categories[]" id="main-select" onchange="getChildrenCategories($(this));">
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @if (@$selected_categories[0]->id == $category->id) selected @endif>
                {{ $category->name }}</option>
        @endforeach
    </select>
    @php
        $selected_categories = is_array($selected_categories) ? null : $selected_categories->slice(1);
    @endphp
    @isset($selected_categories)
        @foreach ($selected_categories as $sub_category)
            <select name="categories[]" class="form-control child-category">
                <option value="{{ $sub_category->id }}">{{ $sub_category->name }}</option>
            </select>
        @endforeach
    @endisset


@endif

@auth('customer')
    @push('scripts')
        <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_1.js') }}"></script>
    @endpush
@endauth
@push('footer')
    <script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_1.js') }}"></script>
@endpush
