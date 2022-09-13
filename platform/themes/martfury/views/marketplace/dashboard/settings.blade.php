@extends(MarketplaceHelper::viewPath('dashboard.layouts.master'))

@section('content')
<div class="ps-card__content">
    {!! Form::open(['route' => 'marketplace.vendor.settings', 'class' => 'ps-form--account-setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
        <div class="ps-form__content">
            <ul class="nav nav-tabs ">
                <li class="nav-item">
                    <a href="#tab_information" class="nav-link active" data-bs-toggle="tab">{{ __('General Information') }}</a>
                </li>
                @include('plugins/marketplace::customers.bank-info-tab')
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_information">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-name" class="required">{{ __('Shop Name') }}</label>
                                <input class="form-control" name="name" id="shop-name" type="text" value="{{ old('name', $store->name) }}" placeholder="{{ __('Shop Name') }}">
                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-phone" class="required">{{ __('Phone Number') }}</label>
                                <input class="form-control" name="phone" id="shop-phone" type="text" value="{{ old('phone', $store->phone) }}" placeholder="{{ __('Shop phone') }}">
                                @if ($errors->has('phone'))
                                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shop-email" class="required">{{ __('Shop Email') }}</label>
                                <input class="form-control" name="email" id="shop-email" type="email" value="{{ old('email', $store->email) }}" placeholder="{{ __('Shop Email') }}">
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <input type="hidden" name="reference_id" value="{{ $store->id }}">
                            <div class="form-group shop-url-wrapper">
                                <label for="shop-url" class="required float-start">{{ __('Shop URL') }}</label>
                                <span class="d-inline-block float-end shop-url-status"></span>
                                <input class="form-control" name="slug" id="shop-url" type="text" value="{{ old('slug', $store->slug) }}" placeholder="{{ __('Shop URL') }}" data-url="{{ route('public.ajax.check-store-url') }}">
                                @if ($errors->has('slug'))
                                    <span class="text-danger">{{ $errors->first('slug') }}</span>
                                @endif
                                <span class="d-inline-block"><small data-base-url="{{ route('public.store', old('slug', '')) }}">{{ route('public.store', old('slug', $store->slug)) }}</small></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('country')) has-error @endif">
                                <label for="country">{{ __('Country') }}</label>
                                <select name="country" class="form-control" id="country">
                                    @foreach(['' => __('Select country...')] + EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                                        <option value="{{ $countryCode }}" @if (old('country', $store->country) == $countryCode) selected @endif>{{ $countryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {!! Form::error('country', $errors) !!}
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('state')) has-error @endif">
                                <label for="state">{{ __('State') }}</label>
                                <input id="state" type="text" class="form-control" name="state" value="{{ old('state', $store->state) }}">
                                {!! Form::error('state', $errors) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('city')) has-error @endif">
                                <label for="city">{{ __('City') }}</label>
                                <input id="city" type="text" class="form-control" name="city" value="{{ old('city', $store->city) }}">
                                {!! Form::error('city', $errors) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="address">{{ __('Address') }}</label>
                                <input id="address" type="text" class="form-control" name="address" value="{{ old('address', $store->address) }}">
                                {!! Form::error('address', $errors) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="logo">{{ __('Logo') }}</label>
                                {!! Form::customImage('logo', old('logo', $store->logo)) !!}
                                {!! Form::error('logo', $errors) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Description') }}</label>
                        <textarea id="description" class="form-control" name="description" rows="3">{{ old('description', $store->description) }}</textarea>
                        {!! Form::error('description', $errors) !!}
                    </div>

                    <div class="form-group">
                        <label for="content">{{ __('Content') }}</label>
                        {!! Form::customEditor('content', old('content', $store->content)) !!}
                        {!! Form::error('content', $errors) !!}
                    </div>
                </div>
                @include('plugins/marketplace::customers.bank-info-content', ['model' => $store->customer])
            </div>


            <div class="form-group text-center">
                <div class="form-group submit">
                    <div class="ps-form__submit text-center">
                        <button class="ps-btn success">{{ __('Save settings') }}</button>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@stop
