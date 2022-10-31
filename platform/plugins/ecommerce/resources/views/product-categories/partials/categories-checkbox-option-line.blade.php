<?php
/**
 * @var string $value
 */
if (isset($value)) {
    $product_categories = $value['selectedCategories'];
    $sub_1_category = $value['sub_1_category'];
    $sub_2_category = $value['sub_2_category'];
}

?>
@if ($categories)
    <select class="form-control" required name="parent_id" id="parent_id"
        data-route="{{ route('marketplace.vendor.products.get_children_categories') }}">
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @if (@$product_categories[0] == $category->id) selected @endif>
                {{ $category->name }}</option>
        @endforeach
    </select>

    <select class="form-control" name="sub_1_id" id="sub_1_id">
        @isset($sub_1_category)
            <option value="{{ $sub_1_category->id }}">
                {{ $sub_1_category->name }}</option>
        @endisset
    </select>

    <select class="form-control" name="sub_2_id" id="sub_2_id">
        @isset($sub_2_category)
            <option value="{{ $sub_2_category->id }}">
                {{ $sub_2_category->name }}</option>
        @endisset
    </select>


    {{-- <ul>
        @foreach ($categories as $category)
            @if ($category->id != $currentId)
                <li value="{{ $category->id ?? '' }}" {{ $category->id == $value ? 'selected' : '' }}>
                    {!! Form::customCheckbox([[$name, $category->id, $category->name, in_array($category->id, $value)]]) !!}
                    @include('plugins/ecommerce::product-categories.partials.categories-checkbox-option-line',
                        [
                            'categories' => $category->child_cats,
                            'value' => $value,
                            'currentId' => $currentId,
                            'name' => $name,
                        ])
                </li>
            @endif
        @endforeach
    </ul> --}}
@endif


<script src="{{ asset('vendor/core/plugins/ecommerce/js/product-vendor-create/step_1.js') }}"></script>
<script>
    @isset($sub_1_category)
        sub_1_id_select.show();
    @endisset
    @isset($sub_2_category)
        sub_2_id_select.show();
    @endisset
</script>
