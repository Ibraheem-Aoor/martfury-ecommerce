<div class="form-group">
    <label class="control-label">Time</label>
    <input type="text" name="time" value="{{ Arr::get($attributes, 'time') }}" class="form-control" placeholder="Time">
</div>

<div class="form-group">
    <label class="control-label">Image</label>
    {!! Form::mediaImage('image', Arr::get($attributes, 'image')) !!}
</div>
