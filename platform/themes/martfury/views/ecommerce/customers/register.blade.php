<div class="ps-my-account">
    <div class="container">
        <form class="ps-form--account ps-tab-root" method="POST" action="{{ route('customer.register.post') }}">
            @csrf
            <div class="ps-form__content">
                <h4>{{ __('Register An Account') }}</h4>
                <div class="form-group">
                    <input class="form-control" name="name" id="txt-name" type="text" value="{{ old('name') }}" placeholder="{{ __('Your Name') }}">
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input class="form-control" name="email" id="txt-email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="{{ __('Your Email') }}">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password" id="txt-password" autocomplete="new-password" placeholder="{{ __('Password') }}">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password_confirmation" id="txt-password-confirmation" autocomplete="new-password" placeholder="{{ __('Password') }}">
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
                @if (is_plugin_active('marketplace'))
                    <div class="show-if-vendor" @if (old('is_vendor') == 0) style="display: none" @endif>
                        <div class="form-group">
                            <label for="shop-name" class="required">{{ __('Shop Name') }}</label>
                            <input class="form-control" name="shop_name" id="shop-name" type="text" value="{{ old('shop_name') }}" placeholder="{{ __('Shop Name') }}">
                            @if ($errors->has('shop_name'))
                                <span class="text-danger">{{ $errors->first('shop_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group shop-url-wrapper">
                            <label for="shop-url" class="required float-left">{{ __('Shop URL') }}</label>
                            <span class="d-inline-block float-right shop-url-status"></span>
                            <input class="form-control" name="shop_url" id="shop-url" type="text" value="{{ old('shop_url') }}" placeholder="{{ __('Shop URL') }}" data-url="{{ route('public.ajax.check-store-url') }}">
                            @if ($errors->has('shop_url'))
                                <span class="text-danger">{{ $errors->first('shop_url') }}</span>
                            @endif
                            <span class="d-inline-block"><small data-base-url="{{ route('public.store', '') }}">{{ route('public.store', (string)old('shop_url')) }}</small></span>
                        </div>
                        <div class="form-group">
                            <label for="shop-phone" class="required">{{ __('Phone Number') }}</label>
                            <input class="form-control" name="shop_phone" id="shop-phone" type="text" value="{{ old('shop_phone') }}" placeholder="{{ __('Shop phone') }}">
                            @if ($errors->has('shop_phone'))
                                <span class="text-danger">{{ $errors->first('shop_phone') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group user-role">
                        <p>
                            <label>
                                <input type="radio" name="is_vendor" value="0" @if (old('is_vendor') == 0) checked="checked" @endif>
                                <span class="d-inline-block">
                                    {{ __('I am a customer') }}
                                </span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="radio" name="is_vendor" value="1" @if (old('is_vendor') == 1) checked="checked" @endif>
                                <span class="d-inline-block">
                                    {{ __('I am a vendor') }}
                                </span>
                            </label>
                        </p>
                    </div>
                @endif
                <div class="form-group">
                    <p>{{ __('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy.') }}</p>
                </div>
                <div class="form-group">
                    <div class="ps-checkbox">
                        <input type="hidden" name="agree_terms_and_policy" value="0">
                        <input class="form-control" type="checkbox" name="agree_terms_and_policy" id="agree-terms-and-policy" value="1">
                        <label for="agree-terms-and-policy">{{ __('I agree to terms & Policy.') }}</label>
                    </div>
                    @if ($errors->has('agree_terms_and_policy'))
                        <span class="text-danger">{{ $errors->first('agree_terms_and_policy') }}</span>
                    @endif
                </div>
                @if (is_plugin_active('captcha') && setting('enable_captcha') && get_ecommerce_setting('enable_recaptcha_in_register_page', 0))
                    <div class="form-group">
		       {!! Captcha::display() !!}
                    </div>
                @endif
                <div class="form-group submit">
                    <button class="ps-btn ps-btn--fullwidth" type="submit">{{ __('Sign up') }}</button>
                </div>

                <div class="form-group">
                    <p class="text-center">{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}" class="d-inline-block">{{ __('Log in') }}</a></p>
                </div>
            </div>
            <div class="ps-form__footer">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
            </div>
        </form>
    </div>
</div>

