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

<!-- Brand Modal -->
<div class="modal fade" id="new_brand_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ADD NEW BRAND</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add_new_brand" action="{{ route('vendor.brand.create') }}">
                <div class="modal-body">
                    <form action="" class="form-group">
                        <label for="">{{ __('Name') }}</label>
                        <input type="text" class="form-control" name="name">
                    </form>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                    <button type="button" onclick="$('#add_new_brand').submit();" class="btn btn-primary">ADD</button>
                </div>
            </form>

        </div>
    </div>
</div>

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
