@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')

    <div class="ps-section--account-setting">
        <div class="ps-section__header">
            <h3>{{ SeoHelper::getTitle() }}</h3>
        </div>
        <div class="ps-section__content">
            <p><i class="icon-user"></i> <span class="d-inline-block">{{ __('Name') }}:</span> <strong>{{ auth('customer')->user()->name }}</strong></p>
            <p><i class="icon-calendar-31"></i> <span class="d-inline-block">{{ __('Date of birth') }}:</span> <strong>{{ auth('customer')->user()->dob ? auth('customer')->user()->dob : __('N/A') }}</strong></p>
            <p><i class="icon-envelope"></i> <span class="d-inline-block">{{ __('Email') }}:</span> <strong>{{ auth('customer')->user()->email }}</strong></p>
            <p><i class="icon-phone-bubble"></i> <span class="d-inline-block">{{ __('Phone') }}:</span> <strong>{{ auth('customer')->user()->phone ? auth('customer')->user()->phone : __('N/A') }}</strong></p>
        </div>
    </div>

@endsection

