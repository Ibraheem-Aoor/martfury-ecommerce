@extends('core/base::layouts.master')
@section('content')
    <div class="row">
        <div class="form-group">
            <form data-route="{{ auth('customer')->check() ? 'product-ean-check-vendor' : route('products.ean_check') }}"
                id="ean_code_check" action="{{ route('products.ean_check') }}">
                <div class="form-group">
                    <label for="" class="form-text">{{ __('Entet Ean Code') }}:</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="ean_code_check">
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="$('#ean_code_check').submit();"
                            class="btn btn-outline-success">{{ __('Submit') }}</button>
                    </div>
            </form>
        </div>
    </div>
@endsection

@section('javascript')
    {{-- Start Ean Code Script --}}
    <script>
        $('#ean_code_check').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: "{{ route('products.ean_check') }}",
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
                },
            });
        });
    </script>
    {{-- End Ean Code Script --}}
@endsection
