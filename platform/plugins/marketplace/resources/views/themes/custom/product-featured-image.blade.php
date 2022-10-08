<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="logo">{{ __('Featured Image') }}</label>
            {!! Form::customImage('image', old('logo', $product?->image)) !!}
            {!! Form::error('image', $errors) !!}
        </div>
    </div>
</div>
