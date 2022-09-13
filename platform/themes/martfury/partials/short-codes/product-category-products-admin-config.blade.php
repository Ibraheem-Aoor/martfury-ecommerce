<div class="form-group">
    <label class="control-label">{{ __('Product category ID') }}</label>
    <div class="ui-select-wrapper form-group">
        <select name="category_id" class="ui-select">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @if ($category->id == Arr::get($attributes, 'category_id')) selected @endif>{!! $category->indent_text !!} {{ $category->name }}</option>
            @endforeach
        </select>
        <svg class="svg-next-icon svg-next-icon-size-16">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
        </svg>
    </div>

</div>

<div class="form-group">
    <label class="control-label">{{ __('Limit number of categories') }}</label>
    <input type="number" name="number_of_categories" value="{{ Arr::get($attributes, 'number_of_categories', 3) }}" class="form-control" placeholder="{{ __('Default: 3') }}">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Limit number of products') }}</label>
    <input type="number" name="limit" value="{{ Arr::get($attributes, 'limit') }}" class="form-control" placeholder="{{ __('Unlimited by default') }}">
</div>
