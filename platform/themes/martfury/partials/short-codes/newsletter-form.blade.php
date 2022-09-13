<div class="ps-newsletter mt-40">
    <div class="ps-container newsletter-form">
        <form class="ps-form--newsletter" method="post" action="{{ route('public.newsletter.subscribe') }}">
            <div class="row">
                <div class="col-xl-5">
                    <div class="ps-form__left">
                        <h3>{!! clean($title) !!}</h3>
                        @if ($description)
                            <p>{!! clean($description) !!}</p>
                        @endif
                        @if ($subtitle)
                            <p>{!! clean($subtitle) !!}</p>
                        @endif
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="ps-form__right">
                        @csrf
                        <div class="form-group--nest">
                            <input class="form-control" name="email" type="email" placeholder="{{ __('Email address') }}">
                            <button class="ps-btn" type="submit">{{ __('Subscribe') }}</button>
                        </div>
                        @if (setting('enable_captcha') && is_plugin_active('captcha'))
                            <div style="margin-top: 10px;">
                                {!! Captcha::display() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
