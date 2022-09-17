<?php
/**
 * @var string $value
 */
$value = isset($value) ? (array) $value : [];
?>
@if ($categories)
    <select name="categories"  class="form-control">
        @foreach ($categories as $category)
            <option value="{{$category->id}}">{{$category->name}}</option>
        @endforeach
    </select>

{{--
    <ul>
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
