<div class="customer-address-payment-form">

    @if (EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check())
        <div class="form-group mb-3">
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Login') }}</a></p>
        </div>
    @endif

    @if (auth('customer')->check())
        @php
            $addresses = get_customer_addresses();
            $isAvailableAddress = !$addresses->isEmpty() ? true : false;
            $sessionAddressId = Arr::get($sessionCheckoutData, 'address_id', $isAvailableAddress ? $addresses->first()->id : null);
        @endphp
        <div class="form-group mb-3">

            @if ($isAvailableAddress)
                <label class="control-label" for="address_id">{{ __('Select available addresses') }}:</label>
            @endif

            <div class="list-customer-address" @if (!$isAvailableAddress) style="display: none;" @endif>
                <div class="select--arrow">
                    <select name="address[address_id]" class="form-control address-control-item" id="address_id">
                        <option value="new" @if (old('address.address_id', $sessionAddressId) == 'new') selected @endif>{{ __('Add new address...') }}</option>
                        @if ($isAvailableAddress)
                            @foreach ($addresses as $address)
                                <option
                                    value="{{ $address->id }}"
                                    @if (
                                        ($address->is_default && !$sessionAddressId) ||
                                        ($sessionAddressId == $address->id) ||
                                        (!old('address.address_id', $sessionAddressId) && $loop->first)
                                    )
                                        selected="selected"
                                    @endif
                                >
                                    {{ $address->address }}, {{ $address->city }}, {{ $address->state }}@if (count(EcommerceHelper::getAvailableCountries()) > 1), {{ $address->country_name }} @endif @if (EcommerceHelper::isZipCodeEnabled() && $address->zip_code), {{ $address->zip_code }} @endif</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="fas fa-angle-down"></i>
                </div>
                <br>
                <div class="address-item-selected" @if ($sessionAddressId == 'new') style="display: none;" @endif>
                    @if ($isAvailableAddress)
                        @if ($sessionAddressId && $addresses->contains('id', $sessionAddressId))
                            @include('plugins/ecommerce::orders.partials.address-item', ['address' => $addresses->firstWhere('id', $sessionAddressId)])
                        @elseif ($defaultAddress = get_default_customer_address())
                            @include('plugins/ecommerce::orders.partials.address-item', ['address' => $defaultAddress])
                        @else
                            @include('plugins/ecommerce::orders.partials.address-item', ['address' => Arr::first($addresses)])
                        @endif
                    @endif
                </div>
                <div class="list-available-address" style="display: none;">
                    @if ($isAvailableAddress)
                        @foreach($addresses as $address)
                            <div class="address-item-wrapper" data-id="{{ $address->id }}">
                                @include('plugins/ecommerce::orders.partials.address-item', compact('address'))
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="address-form-wrapper" @if (auth('customer')->check() && $isAvailableAddress && (!empty($sessionAddressId) && $sessionAddressId !== 'new' || empty(Arr::get($sessionCheckoutData, 'state')))) style="display: none;" @endif>
        <div class="row">
            <div class="col-12">
                <div class="form-group mb-3 @if ($errors->has('address.name')) has-error @endif">
                    <input type="text" name="address[name]" id="address_name" placeholder="{{ __('Full Name') }}" class="form-control address-control-item address-control-item-required checkout-input"
                           value="{{ old('address.name', Arr::get($sessionCheckoutData, 'name')) }}">
                    {!! Form::error('address.name', $errors) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="form-group  @if ($errors->has('address.email')) has-error @endif">
                    <input type="text" name="address[email]" id="address_email" placeholder="{{ __('Email') }}" class="form-control address-control-item address-control-item-required checkout-input" value="{{ old('address.email', Arr::get($sessionCheckoutData, 'email')) }}">
                    {!! Form::error('address.email', $errors) !!}
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="form-group  @if ($errors->has('address.phone')) has-error @endif">
                    <input type="text" name="address[phone]" id="address_phone" placeholder="{{ __('Phone') }} {{ EcommerceHelper::isPhoneFieldOptionalAtCheckout() ? __('(optional)') : '' }}" class="form-control address-control-item {{ !EcommerceHelper::isPhoneFieldOptionalAtCheckout() ? 'address-control-item-required' : '' }} checkout-input" value="{{ old('address.phone', Arr::get($sessionCheckoutData, 'phone')) }}">
                    {!! Form::error('address.phone', $errors) !!}
                </div>
            </div>
        </div>

        <div class="row">
            @if (count(EcommerceHelper::getAvailableCountries()) > 1)
                <div class="col-12">
                    <div class="form-group mb-3 @if ($errors->has('address.country')) has-error @endif">
                        <div class="select--arrow">
                            <select name="address[country]" class="form-control address-control-item address-control-item-required" id="address_country">
                                @foreach(['' => __('Select country...')] + EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                                    <option value="{{ $countryCode }}" @if (old('address.country', Arr::get($sessionCheckoutData, 'country')) == $countryCode) selected @endif>{{ $countryName }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-angle-down"></i>
                        </div>
                        {!! Form::error('address.country', $errors) !!}
                    </div>
                </div>
            @else
                <input type="hidden" name="address[country]" id="address_country" value="{{ Arr::first(array_keys(EcommerceHelper::getAvailableCountries())) }}">
            @endif

            <div class="col-sm-6 col-12">
                <div class="form-group mb-3 @if ($errors->has('address.state')) has-error @endif">
                    <input id="address_state" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('State') }}" name="address[state]" value="{{ old('address.state', Arr::get($sessionCheckoutData, 'state')) }}">
                    {!! Form::error('address.state', $errors) !!}
                </div>
            </div>

            <div class="col-sm-6 col-12">
                <div class="form-group  @if ($errors->has('address.city')) has-error @endif">
                    <input id="address_city" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('City') }}" name="address[city]" value="{{ old('address.city', Arr::get($sessionCheckoutData, 'city')) }}">
                    {!! Form::error('address.city', $errors) !!}
                </div>
            </div>

            <div class="col-12">
                <div class="form-group mb-3 @if ($errors->has('address.address')) has-error @endif">
                    <input id="address_address" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('Address') }}" name="address[address]" value="{{ old('address.address', Arr::get($sessionCheckoutData, 'address')) }}">
                    {!! Form::error('address.address', $errors) !!}
                </div>
            </div>

            @if (EcommerceHelper::isZipCodeEnabled())
                <div class="col-12">
                    <div class="form-group mb-3 @if ($errors->has('address.zip_code')) has-error @endif">
                        <input id="address_zip_code" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('Zip code') }}" name="address[zip_code]" value="{{ old('address.zip_code', Arr::get($sessionCheckoutData, 'zip_code')) }}">
                        {!! Form::error('address.zip_code', $errors) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (!auth('customer')->check())
        <div class="row">

            <div class="col-12">
                <div class="form-group mb-3">
                    <input type="checkbox" name="create_account" value="1" id="create_account" @if (empty($errors) && old('create_account') == 1) checked @endif>
                    <label for="create_account" class="control-label" style="padding-left: 5px">{{ __('Register an account with above information?') }}</label>
                </div>
            </div>
        </div>
        <div class="password-group" @if (!$errors->has('password') && !$errors->has('password_confirmation')) style="display: none;" @endif>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="form-control checkout-input" name="password" autocomplete="password" placeholder="{{ __('Password') }}">
                        {!! Form::error('password', $errors) !!}
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <input id="password-confirm" type="password" class="form-control checkout-input" autocomplete="password-confirmation" placeholder="{{ __('Password confirmation') }}" name="password_confirmation">
                        {!! Form::error('password_confirmation', $errors) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
