<script
    src="{{ asset('vendor/core/plugins/marketplace/js/marketplace.js') }}?v={{ MarketplaceHelper::getAssetVersion() }}">
</script>
<script src="{{ Theme::asset()->url('js/marketplace.js') }}?v={{ MarketplaceHelper::getAssetVersion() }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf"]').attr('content'),
        },
    });
</script>

<script>
    $('#add_new_brand').on('submit', function(e) {
        e.preventDefault();
        var form = $('#add_new_brand');
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.is_added) {
                    toastr.success(response.message);
                    $('#new_brand_modal').modal('hide');
                }
            },
            error: function(response) {
                if (response.status == 422) {
                    $.each(response.responseJSON.errors, function(item, message) {
                        toastr.error(message);
                    });
                }
            },
        });
    })
</script>
