<link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">

{!! Assets::renderHeader(['core']) !!}

<link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/themes/default.css') }}?v={{ get_cms_version() }}">

<link rel="stylesheet" href="{{ Theme::asset()->url('fonts/Linearicons/Linearicons/Font/demo-files/demo.css') }}?v={{ MarketplaceHelper::getAssetVersion() }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/marketplace.css') }}?v={{ MarketplaceHelper::getAssetVersion() }}">

@if (BaseHelper::siteLanguageDirection() == 'rtl')
    <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/rtl.css') }}?v={{ get_cms_version() }}">
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/marketplace-rtl.css') }}?v={{ MarketplaceHelper::getAssetVersion() }}">
@endif
