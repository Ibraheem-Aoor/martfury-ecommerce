@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['route' => 'marketplace.settings', 'class' => 'main-setting-form']) !!}
        <div class="max-width-1200">
            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/marketplace::marketplace.settings.title') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/marketplace::marketplace.settings.description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="{{ MarketplaceHelper::getSettingKey('fee_per_order') }}">{{ trans('plugins/marketplace::marketplace.settings.fee_per_order') }}</label>
                            <input type="number" class="next-input" min="0" max="100" name="{{ MarketplaceHelper::getSettingKey('fee_per_order') }}" id="{{ MarketplaceHelper::getSettingKey('fee_per_order') }}" value="{{ MarketplaceHelper::getSetting('fee_per_order', 0) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="{{ MarketplaceHelper::getSettingKey('fee_withdrawal') }}">{{ trans('plugins/marketplace::marketplace.settings.fee_withdrawal') }}</label>
                            <input type="number" class="next-input" name="{{ MarketplaceHelper::getSettingKey('fee_withdrawal') }}" id="{{ MarketplaceHelper::getSettingKey('fee_withdrawal') }}" value="{{ MarketplaceHelper::getSetting('fee_withdrawal', 0) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="check_valid_signature">{{ trans('plugins/marketplace::marketplace.settings.check_valid_signature') }}
                            </label>
                            <label class="me-2">
                                <input type="radio" name="{{ MarketplaceHelper::getSettingKey('check_valid_signature') }}"
                                       value="1"
                                       @if (MarketplaceHelper::getSetting('check_valid_signature', 1)) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label>
                                <input type="radio" name="{{ MarketplaceHelper::getSettingKey('check_valid_signature') }}"
                                       value="0"
                                       @if (!MarketplaceHelper::getSetting('check_valid_signature', 1)) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="verify_vendor">{{ trans('plugins/marketplace::marketplace.settings.verify_vendor') }}
                            </label>
                            <label class="me-2">
                                <input type="radio" name="{{ MarketplaceHelper::getSettingKey('verify_vendor') }}"
                                       value="1"
                                       @if (MarketplaceHelper::getSetting('verify_vendor', 1)) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label>
                                <input type="radio" name="{{ MarketplaceHelper::getSettingKey('verify_vendor') }}"
                                       value="0"
                                       @if (!MarketplaceHelper::getSetting('verify_vendor', 1)) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="enable_product_approval">{{ trans('plugins/marketplace::marketplace.settings.enable_product_approval') }}
                            </label>
                            <label class="me-2">
                                <input type="radio" name="{{ MarketplaceHelper::getSettingKey('enable_product_approval') }}"
                                       value="1"
                                       @if (MarketplaceHelper::getSetting('enable_product_approval', 1)) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label>
                                <input type="radio" name="{{ MarketplaceHelper::getSettingKey('enable_product_approval') }}"
                                       value="0"
                                       @if (!MarketplaceHelper::getSetting('enable_product_approval', 1)) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">
                    &nbsp;
                </div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
