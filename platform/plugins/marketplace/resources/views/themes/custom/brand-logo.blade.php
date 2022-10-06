<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="logo">{{ __('Logo') }}</label>
            {!! Form::customImage('logo', null) !!}
            {!! Form::error('logo', $errors) !!}
        </div>
    </div>
</div>
