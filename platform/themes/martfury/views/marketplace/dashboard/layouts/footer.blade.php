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
