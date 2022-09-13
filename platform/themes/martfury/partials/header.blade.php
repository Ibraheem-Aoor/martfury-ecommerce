<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts-->
        <link href="https://fonts.googleapis.com/css?family={{ urlencode(theme_option('primary_font', 'Work Sans')) }}:300,400,500,600,700&amp;amp;subset=latin-ext" rel="stylesheet" type="text/css">
        <!-- CSS Library-->

        <style>
            :root {
                --color-1st: {{ theme_option('primary_color', '#fcb800') }};
                --color-2nd: {{ theme_option('secondary_color', '#222222') }};
                --primary-font: '{{ theme_option('primary_font', 'Work Sans') }}', sans-serif;
                --button-text-color: {{ theme_option('button_text_color', '#000') }};
                --header-text-color: {{ theme_option('header_text_color', '#000') }};
                --header-button-background-color: {{ theme_option('header_button_background_color', '#000') }};
                --header-button-text-color: {{ theme_option('header_button_text_color', '#fff') }};
                --header-text-hover-color: {{ theme_option('header_text_hover_color', '#fff') }};
                --header-text-accent-color: {{ theme_option('header_text_accent_color', '#222222') }};
                --header-diliver-border-color: {{ hex_to_rgba(theme_option('header_text_color', '#000'), 0.15) }};
            }
        </style>

        @php
            Theme::asset()->remove('language-css');
            Theme::asset()->container('footer')->remove('language-public-js');
            Theme::asset()->container('footer')->remove('simple-slider-owl-carousel-css');
            Theme::asset()->container('footer')->remove('simple-slider-owl-carousel-js');
            Theme::asset()->container('footer')->remove('simple-slider-css');
            Theme::asset()->container('footer')->remove('simple-slider-js');
        @endphp

        {!! Theme::header() !!}
    </head>
    <body @if (Theme::get('pageId')) id="{{ Theme::get('pageId') }}" @endif @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}
        <div id="alert-container"></div>

        @if (theme_option('preloader_enabled', 'no') == 'yes')
            <div id="loader-wrapper">
                <div class="preloader-loading"></div>
                <div class="loader-section section-left"></div>
                <div class="loader-section section-right"></div>
            </div>
        @endif

        @php
            $categoriesWith = ['slugable', 'children', 'children.slugable', 'children.children', 'children.children.slugable', 'metadata'];
            $categories = !is_plugin_active('ecommerce') ? [] : ProductCategoryHelper::getAllProductCategories()
                        ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                        ->where('parent_id', 0)
                        ->loadMissing($categoriesWith);
        @endphp

        {!! Theme::get('topHeader') !!}

        <header class="header header--1" data-sticky="{{ Theme::get('stickyHeader', 'true') }}">
            <div class="header__top">
                <div class="ps-container">
                    <div class="header__left">
                        <div class="menu--product-categories">
                            <div class="menu__toggle"><i class="icon-menu"></i><span> {{ __('Shop by Department') }}</span></div>
                            <div class="menu__content" style="display: none">
                                <ul class="menu--dropdown">
                                    {!! Theme::partial('product-categories-dropdown', compact('categories')) !!}
                                </ul>
                            </div>
                        </div><a class="ps-logo" href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" height="40"></a>
                    </div>
                    @if (is_plugin_active('ecommerce'))
                        <div class="header__center">
                            <form class="ps-form--quick-search" action="{{ route('public.products') }}" data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                                <div class="form-group--icon">
                                    <div class="product-cat-label">{{ __('All') }}</div>
                                    <select class="form-control product-category-select" name="categories[]">
                                        <option value="0">{{ __('All') }}</option>
                                        @foreach (ProductCategoryHelper::getProductCategoriesWithIndent() as $category)
                                            <option value="{{ $category->id }}">{!! $category->indent_text !!} {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="form-control input-search-product" name="q" type="text" placeholder="{{ __("I'm shopping for...") }}" autocomplete="off">
                                <div class="spinner-icon">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </div>
                                <button type="submit">{{ __('Search') }}</button>
                                <div class="ps-panel--search-result"></div>
                            </form>
                        </div>
                        <div class="header__right">
                            <div class="header__actions">
                                {!! apply_filters('before_theme_header_actions', null) !!}
                                <a class="header__extra btn-compare" href="{{ route('public.compare') }}"><i class="icon-chart-bars"></i><span><i>{{ Cart::instance('compare')->count() }}</i></span></a>
                                <a class="header__extra btn-wishlist" href="{{ route('public.wishlist') }}"><i class="icon-heart"></i><span><i>{{ !auth('customer')->check() ? Cart::instance('wishlist')->count() : auth('customer')->user()->wishlist()->count() }}</i></span></a>
                                @if (EcommerceHelper::isCartEnabled())
                                    <div class="ps-cart--mini">
                                        <a class="header__extra btn-shopping-cart" href="{{ route('public.cart') }}"><i class="icon-bag2"></i><span><i>{{ Cart::instance('cart')->count() }}</i></span></a>
                                        <div class="ps-cart--mobile">
                                            {!! Theme::partial('cart') !!}
                                        </div>
                                    </div>
                                @endif
                                {!! apply_filters('after_theme_header_actions', null) !!}
                                <div class="ps-block--user-header">
                                    <div class="ps-block__left"><i class="icon-user"></i></div>
                                    <div class="ps-block__right">
                                        @if (auth('customer')->check())
                                            <a href="{{ route('customer.overview') }}" class="customer-name">{{ auth('customer')->user()->name }}</a>
                                            <a href="{{ route('customer.logout') }}">{{ __('Logout') }}</a>
                                        @else
                                            <a href="{{ route('customer.login') }}">{{ __('Login') }}</a><a href="{{ route('customer.register') }}">{{ __('Register') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <nav class="navigation">
                <div class="ps-container">
                    <div class="navigation__left">
                        <div class="menu--product-categories">
                            <div class="menu__toggle"><i class="icon-menu"></i><span> {{ __('Shop by Department') }}</span></div>
                            <div class="menu__content" style="display: none">
                                <ul class="menu--dropdown">
                                    {!! Theme::partial('product-categories-dropdown', compact('categories')) !!}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__right">
                        {!! Menu::renderMenuLocation('main-menu', [
                            'view'    => 'menu',
                            'options' => ['class' => 'menu'],
                        ]) !!}
                        @if (is_plugin_active('ecommerce'))
                            <ul class="navigation__extra">
                                @if (is_plugin_active('marketplace'))
                                    <li><a href="{{ !auth('customer')->check() ? route('customer.login') : (auth('customer')->user()->is_vendor ? route('marketplace.vendor.dashboard') : route('marketplace.vendor.become-vendor')) }}">{{ __('Sell On Martfury') }}</a></li>
                                @endif
                                <li><a href="{{ route('public.orders.tracking') }}">{{ __('Track your order') }}</a></li>
                                @php $currencies = get_all_currencies(); @endphp
                                @if (count($currencies) > 1)
                                    <li>
                                        <div class="ps-dropdown">
                                            <a href="{{ route('public.change-currency', get_application_currency()->title) }}"><span>{{ get_application_currency()->title }}</span></a>
                                            <ul class="ps-dropdown-menu">
                                                @foreach ($currencies as $currency)
                                                    @if ($currency->id !== get_application_currency_id())
                                                        <li><a href="{{ route('public.change-currency', $currency->title) }}"><span>{{ $currency->title }}</span></a></li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @endif
                                @if (is_plugin_active('language'))
                                    {!! Theme::partial('language-switcher') !!}
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </nav>
        </header>
        @if (Theme::get('headerMobile'))
            {!! Theme::get('headerMobile') !!}
        @else
            {!! Theme::partial('header-mobile') !!}
        @endif
        @if (is_plugin_active('ecommerce'))
            <div class="ps-panel--sidebar" id="cart-mobile" style="display: none">
                <div class="ps-panel__header">
                    <h3>{{ __('Shopping Cart') }}</h3>
                </div>
                <div class="navigation__content">
                    <div class="ps-cart--mobile">
                        {!! Theme::partial('cart') !!}
                    </div>
                </div>
            </div>
            <div class="ps-panel--sidebar" id="navigation-mobile" style="display: none">
                <div class="ps-panel__header">
                    <h3>{{ __('Categories') }}</h3>
                </div>
                <div class="ps-panel__content">
                    <ul class="menu--mobile">
                        {!! Theme::partial('product-categories-dropdown', compact('categories')) !!}
                    </ul>
                </div>
            </div>
        @endif

        <div class="navigation--list">
            <div class="navigation__content">
                <a class="navigation__item ps-toggle--sidebar" href="#menu-mobile"><i class="icon-menu"></i><span> {{ __('Menu') }}</span></a>
                <a class="navigation__item ps-toggle--sidebar" href="#navigation-mobile"><i class="icon-list4"></i><span> {{ __('Categories') }}</span></a>
                <a class="navigation__item ps-toggle--sidebar" href="#search-sidebar"><i class="icon-magnifier"></i><span> {{ __('Search') }}</span></a>
                <a class="navigation__item ps-toggle--sidebar" href="#cart-mobile"><i class="icon-bag2"></i><span> {{ __('Cart') }}</span></a></div>
        </div>

        @if (is_plugin_active('ecommerce'))
            <div class="ps-panel--sidebar" id="search-sidebar" style="display: none">
                <div class="ps-panel__header">
                    <form class="ps-form--search-mobile" action="{{ route('public.products') }}" data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                        <div class="form-group--nest position-relative">
                            <input class="form-control input-search-product" name="q" value="{{ request()->query('q') }}" type="text" autocomplete="off" placeholder="{{ __('Search something...') }}">
                            <div class="spinner-icon">
                                <i class="fa fa-spin fa-spinner"></i>
                            </div>
                            <button type="submit"><i class="icon-magnifier"></i></button>
                            <div class="ps-panel--search-result"></div>
                        </div>
                    </form>
                </div>
                <div class="navigation__content"></div>
            </div>
        @endif
        <div class="ps-panel--sidebar" id="menu-mobile" style="display: none">
            <div class="ps-panel__header">
                <h3>{{ __('Menu') }}</h3>
            </div>
            <div class="ps-panel__content">
                {!! Menu::renderMenuLocation('main-menu', [
                    'view'    => 'menu',
                    'options' => ['class' => 'menu--mobile'],
                ]) !!}

                <ul class="menu--mobile menu--mobile-extra">
                    @if (is_plugin_active('ecommerce'))
                        <li><a href="{{ route('public.orders.tracking') }}"><i class="icon-check-square"></i> {{ __('Track your order') }}</a></li>
                        <li><a href="{{ route('public.compare') }}"><i class="icon-chart-bars"></i> <span>{{ __('Compare') }}</span></a></li>
                        <li><a href="{{ route('public.wishlist') }}"><i class="icon-heart"></i> <span>{{ __('Wishlist') }}</span></a></li>
                        @if (count($currencies) > 1)
                            <li class="menu-item-has-children">
                                <a href="#"><span>{{ get_application_currency()->title }}</span></a>
                                <span class="sub-toggle"></span>
                                <ul class="sub-menu">
                                    @foreach ($currencies as $currency)
                                        @if ($currency->id !== get_application_currency_id())
                                            <li><a href="{{ route('public.change-currency', $currency->title) }}"><span>{{ $currency->title }}</span></a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (is_plugin_active('language'))
                        @php
                            $supportedLocales = Language::getSupportedLocales();
                        @endphp

                        @if ($supportedLocales && count($supportedLocales) > 1)
                            @php
                                $languageDisplay = setting('language_display', 'all');
                            @endphp
                            <li class="menu-item-has-children">
                                <a href="#">
                                    @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                                        {!! language_flag(Language::getCurrentLocaleFlag(), Language::getCurrentLocaleName()) !!}
                                    @endif
                                    @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                                        {{ Language::getCurrentLocaleName() }}
                                    @endif
                                </a>
                                <span class="sub-toggle"></span>
                                <ul class="sub-menu">
                                    @foreach ($supportedLocales as $localeCode => $properties)
                                        @if ($localeCode != Language::getCurrentLocale())
                                            <li>
                                                <a href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}">
                                                    @if ($languageDisplay == 'all' || $languageDisplay == 'flag'){!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}@endif
                                                    @if ($languageDisplay == 'all' || $languageDisplay == 'name')<span>{{ $properties['lang_name'] }}</span>@endif
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
