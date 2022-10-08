<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="logo">{{ __('Image') }}</label>
            {!! Form::customImage('logo' , old('logo' , $brand?->logo)) !!}
            {!! Form::error('logo', $errors) !!}
        </div>
    </div>
</div>

