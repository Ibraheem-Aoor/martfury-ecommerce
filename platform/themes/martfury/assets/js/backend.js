(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    window.botbleCookieNewsletter = (() => {

        const COOKIE_VALUE = 1;
        const COOKIE_NAME = 'botble_cookie_newsletter';
        const COOKIE_DOMAIN = $('div[data-session-domain]').data('session-domain');
        const COOKIE_MODAL = $('#subscribe');
        const COOKIE_MODAL_TIME = COOKIE_MODAL.data('time');

        function newsletterWithCookies(expirationInDays) {
            setCookie(COOKIE_NAME, COOKIE_VALUE, expirationInDays);
        }

        function cookieExists(name) {
            return document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1;
        }

        function hideCookieDialog() {
            if (!cookieExists(COOKIE_NAME) && $('#dont_show_again').is(':checked')) {
                newsletterWithCookies(3);
            } else {
                newsletterWithCookies(1 / 24);
            }
        }

        function setCookie(name, value, expirationInDays) {
            const date = new Date();
            date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value
                + ';expires=' + date.toUTCString()
                + ';domain=' + COOKIE_DOMAIN
                + ';path=/';
        }

        if (!cookieExists(COOKIE_NAME)) {
            setTimeout(function () {
                if (COOKIE_MODAL.length > 0) {
                    COOKIE_MODAL.addClass('active');
                    $('body').css('overflow', 'hidden');
                }
            }, COOKIE_MODAL_TIME);
        }

        return {
            newsletterWithCookies: newsletterWithCookies,
            hideCookieDialog: hideCookieDialog
        };
    })();

    var showError = message => {
        window.showAlert('alert-danger', message);
    }

    var showSuccess = message => {
        window.showAlert('alert-success', message);
    }

    var handleError = data => {
        if (typeof (data.errors) !== 'undefined' && data.errors.length) {
            handleValidationError(data.errors);
        } else if (typeof (data.responseJSON) !== 'undefined') {
            if (typeof (data.responseJSON.errors) !== 'undefined') {
                if (data.status === 422) {
                    handleValidationError(data.responseJSON.errors);
                }
            } else if (typeof (data.responseJSON.message) !== 'undefined') {
                showError(data.responseJSON.message);
            } else {
                $.each(data.responseJSON, (index, el) => {
                    $.each(el, (key, item) => {
                        showError(item);
                    });
                });
            }
        } else {
            showError(data.statusText);
        }
    }

    var handleValidationError = errors => {
        let message = '';
        $.each(errors, (index, item) => {
            if (message !== '') {
                message += '<br />';
            }
            message += item;
        });
        showError(message);
    }

    window.showAlert = (messageType, message) => {
        if (messageType && message !== '') {
            let alertId = Math.floor(Math.random() * 1000);

            let html = `<div class="alert ${messageType} alert-dismissible" id="${alertId}">
                            <span class="close icon-cross2" data-dismiss="alert" aria-label="close"></span>
                            <i class="icon-` + (messageType === 'alert-success' ? 'checkmark-circle' : 'cross-circle') + ` message-icon"></i>
                            ${message}
                        </div>`;

            $('#alert-container').append(html).ready(() => {
                window.setTimeout(() => {
                    $(`#alert-container #${alertId}`).remove();
                }, 6000);
            });
        }
    }

    let isRTL = $('body').prop('dir') === 'rtl';

    $(document).ready(function () {
        window.onBeforeChangeSwatches = function (data) {
            $('.add-to-cart-form .error-message').hide();
            $('.add-to-cart-form .success-message').hide();
            $('.number-items-available').html('').hide();

            if (data && data.attributes) {
                $('.add-to-cart-form button[type=submit]').prop('disabled', true).addClass('btn-disabled');
            }
        }

        window.onChangeSwatchesSuccess = function (res) {
            $('.add-to-cart-form .error-message').hide();
            $('.add-to-cart-form .success-message').hide();

            if (res) {
                let buttonSubmit = $('.add-to-cart-form button[type=submit]');
                if (res.error) {
                    buttonSubmit.prop('disabled', true).addClass('btn-disabled');
                    $('.number-items-available').html('<span class="text-danger">(' + res.message + ')</span>').show();
                    $('.hidden-product-id').val('');
                } else {
                    $('.add-to-cart-form').find('.error-message').hide();
                    $('.ps-product__price span').text(res.data.display_sale_price);
                    if (res.data.sale_price !== res.data.price) {
                        $('.ps-product__price del').text(res.data.display_price).show();
                    } else {
                        $('.ps-product__price del').hide();
                    }

                    if (res.data.sku) {
                        const skuText = $('.ps-product__specification #product-sku');
                        skuText.closest('p').show();
                        skuText.text(res.data.sku);
                    }

                    $('.hidden-product-id').val(res.data.id);
                    buttonSubmit.prop('disabled', false).removeClass('btn-disabled');

                    if (res.data.error_message) {
                        buttonSubmit.prop('disabled', true).addClass('btn-disabled');
                        $('.number-items-available').html('<span class="text-danger">(' + res.data.error_message + ')</span>').show();
                    } else if (res.data.success_message) {
                        $('.number-items-available').html('<span class="text-success">(' + res.data.success_message + ')</span>').show();
                    } else {
                        $('.number-items-available').html('').hide();
                    }

                    const unavailableAttributeIds = res.data.unavailable_attribute_ids || [];
                    $('.attribute-swatch-item').removeClass('pe-none');
                    $('option.product-filter-item').prop('disabled', false);
                    if (unavailableAttributeIds && unavailableAttributeIds.length) {
                        unavailableAttributeIds.map(function (id) {
                            let $item = $('.attribute-swatch-item[data-id="' + id + '"]');
                            if ($item.length) {
                                $item.addClass('pe-none');
                                $item.find('input').prop('checked', false);
                            } else {
                                $item = $('option.product-filter-item[data-id="' + id + '"]');
                                if ($item.length) {
                                    $item.prop('disabled', 'disabled').prop('selected', false);
                                }
                            }
                        });
                    }

                    let slider = $(document).find('.ps-product--quickview .ps-product__images');

                    if (slider.length) {
                        slider.slick('unslick');

                        let imageHtml = '';
                        res.data.image_with_sizes.origin.forEach(function (item) {
                            imageHtml += '<div class="item"><img src="' + item + '" alt="image"/></div>'
                        });

                        slider.html(imageHtml);

                        slider.slick({
                            slidesToShow: slider.data('item'),
                            slidesToScroll: 1,
                            rtl: isRTL,
                            infinite: false,
                            arrows: slider.data('arrow'),
                            focusOnSelect: true,
                            prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
                            nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>",
                        });
                    }

                    var product = $('.ps-product--detail');
                    if (product.length > 0) {

                        let primary = product.find('.ps-product__gallery');
                        let second = product.find('.ps-product__variants');
                        let vertical = product
                            .find('.ps-product__thumbnail')
                            .data('vertical');

                        if (primary.length) {
                            primary.slick('unslick');

                            let imageHtml = '';
                            res.data.image_with_sizes.origin.forEach(function (item) {
                                imageHtml += '<div class="item"><a href="' + item + '"><img src="' + item + '" alt="' + res.data.name + '"/></a></div>';
                            });

                            primary.html(imageHtml);

                            primary.slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                rtl: isRTL,
                                asNavFor: '.ps-product__variants',
                                fade: true,
                                dots: false,
                                infinite: false,
                                arrows: primary.data('arrow'),
                                prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
                                nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>",
                            });
                        }

                        if (second.length) {

                            second.slick('unslick');

                            let thumbHtml = '';
                            res.data.image_with_sizes.thumb.forEach(function (item) {
                                thumbHtml += '<div class="item"><img src="' + item + '" alt="' + res.data.name + '"/></div>';
                            });

                            second.html(thumbHtml);

                            second.slick({
                                slidesToShow: second.data('item'),
                                slidesToScroll: 1,
                                rtl: isRTL,
                                infinite: false,
                                arrows: second.data('arrow'),
                                focusOnSelect: true,
                                prevArrow: "<a href='#'><i class='fa fa-angle-up'></i></a>",
                                nextArrow: "<a href='#'><i class='fa fa-angle-down'></i></a>",
                                asNavFor: '.ps-product__gallery',
                                vertical: vertical,
                                responsive: [
                                    {
                                        breakpoint: 1200,
                                        settings: {
                                            arrows: second.data('arrow'),
                                            slidesToShow: 4,
                                            vertical: false,
                                            prevArrow:
                                                "<a href='#'><i class='fa fa-angle-left'></i></a>",
                                            nextArrow:
                                                "<a href='#'><i class='fa fa-angle-right'></i></a>",
                                        },
                                    },
                                    {
                                        breakpoint: 992,
                                        settings: {
                                            arrows: second.data('arrow'),
                                            slidesToShow: 4,
                                            vertical: false,
                                            prevArrow:
                                                "<a href='#'><i class='fa fa-angle-left'></i></a>",
                                            nextArrow:
                                                "<a href='#'><i class='fa fa-angle-right'></i></a>",
                                        },
                                    },
                                    {
                                        breakpoint: 480,
                                        settings: {
                                            slidesToShow: 3,
                                            vertical: false,
                                            prevArrow:
                                                "<a href='#'><i class='fa fa-angle-left'></i></a>",
                                            nextArrow:
                                                "<a href='#'><i class='fa fa-angle-right'></i></a>",
                                        },
                                    },
                                ],
                            });
                        }
                    }

                    $(window).trigger('resize');

                    if (product.length > 0) {
                        let $gallery = product.find('.ps-product__gallery');
                        if ($gallery.data('lightGallery')) {
                            $gallery.data('lightGallery').destroy(true);
                        }

                        $gallery.lightGallery({
                            selector: '.item a',
                            thumbnail: true,
                            share: false,
                            fullScreen: false,
                            autoplay: false,
                            autoplayControls: false,
                            actualSize: false,
                        });
                    }
                }
            }
        };

        $('.ps-panel--sidebar').show();
        $('.ps-popup').show();
        $('.menu--product-categories .menu__content').show();

        $('.ps-popup__close').on('click', function (e) {
            e.preventDefault();
            $(this)
                .closest('.ps-popup')
                .removeClass('active');
            $('body').css('overflow', 'auto');

            window.botbleCookieNewsletter.hideCookieDialog();
        });

        $('#subscribe').on('click', function (event) {
            if (!$(event.target).closest('.ps-popup__content').length) {
                $(this).removeClass('active');
                $('body').css('overflow-y', 'auto');

                window.botbleCookieNewsletter.hideCookieDialog();
            }
        });

        $(document).on('click', '.newsletter-form button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.closest('form').prop('action'),
                data: new FormData(_self.closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    _self.removeClass('button-loading');

                    if (typeof refreshRecaptcha !== 'undefined') {
                        refreshRecaptcha();
                    }

                    if (!res.error) {
                        window.botbleCookieNewsletter.newsletterWithCookies(30);
                        _self.closest('form').find('input[type=email]').val('');
                        showSuccess(res.message);
                        setTimeout(() => {
                            _self.closest('.modal-body').find('button[data-dismiss="modal"]').trigger('click');
                        }, 2000);
                    } else {
                        showError(res.message);
                    }
                },
                error: res => {
                    if (typeof refreshRecaptcha !== 'undefined') {
                        refreshRecaptcha();
                    }
                    _self.removeClass('button-loading');
                    handleError(res);
                }
            });
        });

        $(document).on('click', '.ps-form--download-app button[type=submit]', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass('button-loading');

            $.ajax({
                url: _self.closest('form').prop('action'),
                data: _self.closest('form').serialize(),
                type: 'POST',
                success: res => {
                    if (res.error) {
                        _self.removeClass('button-loading');
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);
                    _self.removeClass('button-loading');
                },
                error: res => {
                    _self.removeClass('button-loading');
                    handleError(res, _self.closest('form'));
                }
            });
        });

        const $layoutShop = $('.ps-layout--shop');
        if($layoutShop.length > 0) {
            $(document).on('click', '#products-filter-sidebar', function(e) {
                e.preventDefault();
                $layoutShop.find('.ps-layout__left').toggleClass('active');
            });

            $('.ps-layout__left .ps-filter__header .ps-btn--close').on(
                'click',
                function(e) {
                    e.preventDefault();
                    $layoutShop.find('.ps-layout__left').toggleClass('active');
                }
            );
            $(document).on('click', '.ps-layout__left .screen-darken', function (e) {
                e.preventDefault();
                $layoutShop.find('.ps-layout__left').toggleClass('active');
            });
            $('.ps-select-shop-sort').on('change', function(e) {
                $formSearch.find('input[name=sort-by]').val($(e.currentTarget).val());
                $formSearch.trigger('submit');
            });
        }

        const $formSearch = $('#products-filter-form');
        const productListing = '.ps-products-listing';
        const $productListing = $(productListing);
        let isReadySubmitTrigger = true;
        $(document).on('change', '.product-filter-item', function () {
            if (isReadySubmitTrigger) {
                $(this).closest('form').trigger('submit');
            }
        });

        function changeInputInSearchForm(parseParams) {
            isReadySubmitTrigger = false;
            $formSearch.find('input, select, textarea').each(function (e, i) {
                const $el = $(i);
                const name = $el.attr('name');
                let value = parseParams[name] || null;
                const type = $el.attr('type');
                switch (type) {
                    case 'checkbox':
                        $el.prop('checked', false);
                        if (Array.isArray(value)) {
                            $el.prop('checked', value.includes($el.val()));
                        } else {
                            $el.prop('checked', !!value);
                        }
                        break;
                    default:
                        if ($el.is('[name=max_price]')) {
                            $el.val(value || $el.data('max'));
                        } else if ($el.is('[name=min_price]')) {
                            $el.val(value || $el.data('min'));
                        } else if ($el.val() != value) {
                            $el.val(value);
                        }
                        break;
                }
            });
            isReadySubmitTrigger = true;
        }
        function convertFromDataToArray(formData) {
            let data = [];
            formData.forEach(function (obj) {
                if (obj.value) {
                    // break with price
                    if (['min_price', 'max_price'].includes(obj.name)) {
                        const dataValue = $formSearch.find('input[name=' + obj.name +']').data(obj.name.substring(0, 3));
                        if (dataValue == parseInt(obj.value)) {
                            return;
                        }
                    }
                    data.push(obj);
                }
            });
            return data;
        }
        $(document).on('click', '.ps-shopping .prodducts-layout li:not(.active) a', function (e) {
            e.preventDefault();
            const $this = $(e.currentTarget);
            $this.closest('ul').find('li').removeClass('active');
            $this.closest('li').addClass('active')
            $formSearch.find('input[name=layout]').val($this.data('layout')).trigger('change');
        });
        if ($formSearch.length) {
            $(document).on('submit', '#products-filter-form', function (event) {
                event.preventDefault();
                const $form = $(event.currentTarget);
                const formData = $form.serializeArray();
                let data = convertFromDataToArray(formData);
                let uriData = [];

                // Paginate
                const $elPage = $productListing.find('input[name=page]');
                if ($elPage.val()) {
                    data.push({name: 'page', value: $elPage.val()});
                }

                // Without "s" param
                data.map(function (obj) {
                    uriData.push(encodeURIComponent(obj.name) + '=' + obj.value);
                })

                const nextHref = $form.attr('action') + (uriData && uriData.length ? ('?' + uriData.join('&')) : '');

                // add to params get to popstate not show json
                data.push({name: '_', value: +new Date()});

                $.ajax({
                    url: $form.attr('action'),
                    type: 'GET',
                    data: data,
                    beforeSend: function () {
                        // Show loading befor send
                        $productListing.find('.loading').show();
                        // Animation scroll to filter button
                        $('html, body').animate({
                            scrollTop: $('.ps-shopping').offset().top - 200
                        }, 500);
                        // Change price step;
                        const priceStep = $formSearch.find('.nonlinear');
                        if (priceStep.length) {
                            priceStep[0].noUiSlider.set([$formSearch.find('input[name=min_price]').val(), $formSearch.find('input[name=max_price]').val()]);
                        }
                        $('.ps-layout__left').removeClass('active'); // Close sidebar filter
                    },
                    success: function (res) {
                        if (res.error == false) {
                            $productListing.html(res.data);
                            const total = res.message;
                            if (total) {
                                $('.ps-shopping .products-found').html('<strong>' + total.substr(0, total.indexOf(' ')) +
                                    '</strong><span class="ml-1">' + total.substr(total.indexOf(' ')+1) + '</span>')
                            }

                            if (res.additional && res.additional.breadcrumb) {
                                $('.ps-breadcrumb .ps-container').html(res.additional.breadcrumb);
                            }

                            if (nextHref != window.location.href) {
                                window.history.pushState(data, res.message, nextHref);
                            }
                        } else {
                            showError(res.message || 'Opp!');
                        }
                    },
                    error: function (res) {
                        handleError(res);
                    },
                    complete: function () {
                        $productListing.find('.loading').hide();
                    }
                });
            });

            window.addEventListener('popstate', function (event) {
                let url = window.location.origin + window.location.pathname;
                if ($formSearch.length) {
                    $formSearch.attr('action', url)
                    const parseParams = parseParamsSearch();
                    changeInputInSearchForm(parseParams);
                    $formSearch.trigger('submit');
                } else {
                    history.back()
                }
            }, false);

            $(document).on('click', productListing + ' .pagination a', function (e) {
                e.preventDefault();
                var url = new URL($(e.currentTarget).attr('href'));
                var page = url.searchParams.get("page");
                $productListing.find('input[name=page]').val(page)
                $formSearch.trigger('submit');
            });
        }

        $(document).on('click', '.ps-list--categories li a', function(e) {
            e.preventDefault();
            const $this = $(e.currentTarget);
            let href = $this.attr('href');
            const $li = $this.parent();
            if ($li.hasClass('current-menu-item')) {
                $li.removeClass('current-menu-item');
                href = $formSearch.data('action');
            } else {
                const $ul = $this.closest('.ps-list--categories');
                $ul.find('li').removeClass('current-menu-item');
                $li.addClass('current-menu-item');
            }

            $formSearch.attr('action', href).trigger('submit');
        });

        function parseParamsSearch(query, includeArray = false) {
            var pairs = query || window.location.search.substring(1);
            var re = /([^&=]+)=?([^&]*)/g;
            var decodeRE = /\+/g;  // Regex for replacing addition symbol with a space
            var decode = function (str) {return decodeURIComponent( str.replace(decodeRE, " ") );};
            var params = {}, e;
            while ( e = re.exec(pairs) ) {
                var k = decode( e[1] ), v = decode( e[2] );
                if (k.substring(k.length - 2) == '[]') {
                    if (includeArray) {
                        k = k.substring(0, k.length - 2);
                    }
                    (params[k] || (params[k] = [])).push(v);
                }
                else params[k] = v;
            }
            return params;
        }

        $(document).on('click', '.js-add-to-wishlist-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'POST',
                success: res => {

                    if (res.error) {
                        _self.removeClass('button-loading');
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);

                    $('.btn-wishlist span i').text(res.data.count);

                    _self.removeClass('button-loading').removeClass('js-add-to-wishlist-button').addClass('js-remove-from-wishlist-button active');
                },
                error: res => {
                    _self.removeClass('button-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.js-remove-from-wishlist-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'DELETE',
                success: res => {

                    if (res.error) {
                        _self.removeClass('button-loading');
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);

                    $('.btn-wishlist span i').text(res.data.count);

                    _self.closest('tr').remove();
                    _self.removeClass('button-loading').removeClass('js-remove-from-wishlist-button active').addClass('js-add-to-wishlist-button');
                },
                error: res => {
                    _self.removeClass('button-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.js-add-to-compare-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'POST',
                success: res => {

                    if (res.error) {
                        _self.removeClass('button-loading');
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);

                    $('.btn-compare span i').text(res.data.count);

                    _self.removeClass('button-loading').removeClass('js-add-to-compare-button').addClass('js-remove-from-compare-button active');
                },
                error: res => {
                    _self.removeClass('button-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.js-remove-from-compare-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'DELETE',
                success: res => {

                    if (res.error) {
                        _self.removeClass('button-loading');
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    _self.removeClass('button-loading').addClass('js-add-to-compare-button').removeClass('js-remove-from-compare-button active');

                    window.showAlert('alert-success', res.message);

                    $('.btn-compare span i').text(res.data.count);

                    $('.ps-table--compare').load(window.location.href + ' .ps-table--compare > *');
                },
                error: res => {
                    _self.removeClass('button-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.add-to-cart-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.prop('disabled', true).addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'POST',
                data: {
                    id: _self.data('id')
                },
                dataType: 'json',
                success: res => {
                    _self.prop('disabled', false).removeClass('button-loading').addClass('active');

                    if (res.error) {
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);

                    if (_self.prop('name') === 'checkout' && res.data.next_url !== undefined) {
                        window.location.href = res.data.next_url;
                    } else {
                        $.ajax({
                            url: window.siteUrl + '/ajax/cart',
                            method: 'GET',
                            success: response => {
                                if (!response.error) {
                                    $('.ps-cart--mobile').html(response.data.html);
                                    $('.btn-shopping-cart span i').text(response.data.count);
                                }
                            }
                        });
                    }
                },
                error: res => {
                    _self.prop('disabled', false).removeClass('button-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.remove-cart-item', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.closest('.ps-product--cart-mobile').addClass('content-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'GET',
                success: res => {
                    _self.closest('.ps-product--cart-mobile').removeClass('content-loading');

                    if (res.error) {
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    $.ajax({
                        url: window.siteUrl + '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('.ps-cart--mobile').html(response.data.html);
                                $('.btn-shopping-cart span i').text(response.data.count);
                                window.showAlert('alert-success', res.message);
                            }
                        }
                    });
                },
                error: res => {
                    _self.closest('.ps-product--cart-mobile').removeClass('content-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.remove-cart-button', function (event) {
            event.preventDefault();
            let _self = $(this);

            _self.closest('.ps-table--shopping-cart').addClass('content-loading');

            $.ajax({
                url: _self.data('url'),
                method: 'GET',
                success: function (res) {

                    if (res.error) {
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    $('.ps-shopping-cart').load(window.location.href + ' .ps-shopping-cart > *', function () {
                        _self.closest('.ps-table--shopping-cart').removeClass('content-loading');
                        window.showAlert('alert-success', res.message);
                    });

                    $.ajax({
                        url: window.siteUrl + '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                $('.ps-cart--mobile').html(response.data.html);
                                $('.btn-shopping-cart span i').text(response.data.count);
                            }
                        }
                    });
                },
                error: res => {
                    _self.closest('.ps-table--shopping-cart').removeClass('content-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        });

        $(document).on('click', '.add-to-cart-form button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let _self = $(this);

            if (!$('.hidden-product-id').val()) {
                _self.prop('disabled', true).addClass('btn-disabled');
                return;
            }

            _self.prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

            _self.closest('form').find('.error-message').hide();
            _self.closest('form').find('.success-message').hide();

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.closest('form').prop('action'),
                data: new FormData(_self.closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');

                    if (res.error) {
                        _self.removeClass('button-loading');
                        window.showAlert('alert-danger', res.message);
                        return false;
                    }

                    window.showAlert('alert-success', res.message);

                    if (_self.prop('name') === 'checkout' && res.data.next_url !== undefined) {
                        window.location.href = res.data.next_url;
                    } else {
                        $.ajax({
                            url: window.siteUrl + '/ajax/cart',
                            method: 'GET',
                            success: function (response) {
                                if (!response.error) {
                                    $('.ps-cart--mobile').html(response.data.html);
                                    $('.btn-shopping-cart span i').text(response.data.count);
                                }
                            }
                        });
                    }
                },
                error: res => {
                    _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                    handleError(res, _self.closest('form'));
                }
            });
        });

        function ajaxUpdateCart(_self) {
            _self.closest('.ps-table--shopping-cart').addClass('content-loading');

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.closest('form').prop('action'),
                data: new FormData(_self.closest('form')[0]),
                contentType: false,
                processData: false,
                success: res => {
                    if (res.error) {
                        window.showAlert('alert-danger', res.message);
                        _self.closest('.ps-table--shopping-cart').removeClass('content-loading');
                        _self.closest('.product__qty').find('.qty-input').val(res.data.count).prop('placeholder', res.data.count);
                        return false;
                    }

                    $('.ps-section--shopping').load(window.location.href + ' .ps-section--shopping > *');

                    $.ajax({
                        url: window.siteUrl + '/ajax/cart',
                        method: 'GET',
                        success: response => {
                            if (!response.error) {
                                _self.closest('.ps-table--shopping-cart').removeClass('content-loading');
                                $('.ps-cart--mobile').html(response.data.html);
                                $('.btn-shopping-cart span i').text(response.data.count);
                                window.showAlert('alert-success', res.message);
                            }
                        },
                        error: res => {
                            _self.closest('.ps-table--shopping-cart').removeClass('content-loading');
                            window.showAlert('alert-danger', res.message);
                        }
                    });
                },
                error: res => {
                    _self.closest('.ps-table--shopping-cart').removeClass('content-loading');
                    window.showAlert('alert-danger', res.message);
                }
            });
        }

        $(document).on('change', '.submit-form-on-change', function () {
            $(this).closest('form').submit();
        });

        let imagesReviewBuffer = [];
        let setImagesFormReview = function (input) {
            const dT = new ClipboardEvent('').clipboardData || // Firefox < 62 workaround exploiting https://bugzilla.mozilla.org/show_bug.cgi?id=1422655
                new DataTransfer(); // specs compliant (as of March 2018 only Chrome)
            for (let file of imagesReviewBuffer) {
                dT.items.add(file);
            }
            input.files = dT.files;
            loadPreviewImage(input);
        }

        let loadPreviewImage = function (input) {
            let $uploadText = $('.image-upload__text');
            const maxFiles = $(input).data('max-files');
            let filesAmount = input.files.length;

            if (maxFiles) {
                if (filesAmount >= maxFiles) {
                    $uploadText.closest('.image-upload__uploader-container').addClass('d-none');
                } else {
                    $uploadText.closest('.image-upload__uploader-container').removeClass('d-none');
                }
                $uploadText.text(filesAmount + '/' + maxFiles);
            } else {
                $uploadText.text(filesAmount);
            }
            const viewerList = $('.image-viewer__list');
            const $template = $('#review-image-template').html();

            viewerList.addClass('is-loading');
            viewerList.find('.image-viewer__item').remove();

            if (filesAmount) {
                for (let i = filesAmount - 1; i >= 0; i--) {
                    viewerList.prepend($template.replace('__id__', i));
                }
                for (let j = filesAmount - 1; j >= 0; j--) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        viewerList
                            .find('.image-viewer__item[data-id=' + j + ']')
                            .find('img')
                            .attr('src', event.target.result);
                    }
                    reader.readAsDataURL(input.files[j]);
                }
            }
            viewerList.removeClass('is-loading')
        }

        $(document).on('change', '.form-review-product input[type=file]', function (event) {
            event.preventDefault();
            let input = this;
            let $input = $(input);
            let maxSize = $input.data('max-size');
            Object.keys(input.files).map(function(i) {
                if (maxSize && (input.files[i].size / 1024) > maxSize) {
                    let message = $input.data('max-size-message')
                        .replace('__attribute__', input.files[i].name)
                        .replace('__max__', maxSize)
                    window.showAlert('alert-danger', message);
                } else {
                    imagesReviewBuffer.push(input.files[i]);
                }
            });

            let filesAmount = imagesReviewBuffer.length;
            const maxFiles = $input.data('max-files');
            if (maxFiles && filesAmount > maxFiles) {
                imagesReviewBuffer.splice(filesAmount - maxFiles - 1, filesAmount - maxFiles);
            }

            setImagesFormReview(input);
        });

        $(document).on('click', '.form-review-product .image-viewer__icon-remove', function (event) {
            event.preventDefault();
            const $this = $(event.currentTarget);
            let id = $this.closest('.image-viewer__item').data('id');
            imagesReviewBuffer.splice(id, 1);

            let input = $('.form-review-product input[type=file]')[0];
            setImagesFormReview(input);
        });

        if (sessionStorage.reloadReviewsTab) {
            $('.ps-tab-list li a[href="#tab-reviews"]').trigger('click');
            sessionStorage.reloadReviewsTab = false;
        }

        $(document).on('click', '.form-review-product button[type=submit]', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

            const $form = $(this).closest('form');
            $.ajax({
                type: 'POST',
                cache: false,
                url: $form.prop('action'),
                data: new FormData($form[0]),
                contentType: false,
                processData: false,
                success: res => {
                    if (!res.error) {
                        $form.find('select').val(0);
                        $form.find('textarea').val('');

                        showSuccess(res.message);

                        setTimeout(function () {
                            sessionStorage.reloadReviewsTab = true;
                            window.location.reload();
                        }, 1500);
                    } else {
                        showError(res.message);
                    }

                    $(this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                },
                error: res => {
                    $(this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                    handleError(res, $form);
                }
            });
        });

        $('.form-coupon-wrapper .coupon-code').keypress(event => {
            if (event.keyCode === 13) {
                $('.apply-coupon-code').trigger('click');
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        });

        $(document).on('click', '.btn-apply-coupon-code', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                type: 'POST',
                data: {
                    coupon_code: _self.closest('.form-coupon-wrapper').find('.coupon-code').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: res => {
                    if (!res.error) {
                        $('.ps-section--shopping').load(window.location.href + '?applied_coupon=1 .ps-section--shopping > *', function () {
                            _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                            window.showAlert('alert-success', res.message);
                        });
                    } else {
                        window.showAlert('alert-danger', res.message);
                        _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                    }
                },
                error: data => {
                    if (typeof (data.responseJSON) !== 'undefined') {
                        if (data.responseJSON.errors !== 'undefined') {
                            $.each(data.responseJSON.errors, (index, el) => {
                                $.each(el, (key, item) => {
                                    window.showAlert('alert-danger', item);
                                });
                            });
                        } else if (typeof (data.responseJSON.message) !== 'undefined') {
                            window.showAlert('alert-danger', data.responseJSON.message);
                        }
                    } else {
                        window.showAlert('alert-danger', data.status.text);
                    }
                    _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
                }
            });
        });

        $(document).on('click', '.btn-remove-coupon-code', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            let buttonText = _self.text();
            _self.text(_self.data('processing-text'));

            $.ajax({
                url: _self.data('url'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: res => {
                    if (!res.error) {
                        $('.ps-section--shopping').load(window.location.href + ' .ps-section--shopping > *', function () {
                            _self.text(buttonText);
                        });
                    } else {
                        window.showAlert('alert-danger', res.message);
                        _self.text(buttonText);
                    }
                },
                error: data => {
                    if (typeof (data.responseJSON) !== 'undefined') {
                        if (data.responseJSON.errors !== 'undefined') {
                            $.each(data.responseJSON.errors, (index, el) => {
                                $.each(el, (key, item) => {
                                    window.showAlert('alert-danger', item);
                                });
                            });
                        } else if (typeof (data.responseJSON.message) !== 'undefined') {
                            window.showAlert('alert-danger', data.responseJSON.message);
                        }
                    } else {
                        window.showAlert('alert-danger', data.status.text);
                    }
                    _self.text(buttonText);
                }
            });
        });

        function number_format(number, decimals, dec_point, thousands_sep) {
            let n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                toFixedFix = function (n, prec) {
                    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                    let k = Math.pow(10, prec);
                    return Math.round(n * k) / k;
                },
                s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');

            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }

            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        function filterSlider() {
            $('.nonlinear').each(function (index, element) {
                let $element = $(element);
                let min = $element.data('min');
                let max = $element.data('max');
                let $wrapper = $(element).closest('.nonlinear-wrapper');
                noUiSlider.create(element, {
                    connect: true,
                    behaviour: 'tap',
                    start: [$wrapper.find('.product-filter-item-price-0').val(), $wrapper.find('.product-filter-item-price-1').val()],
                    range: {
                        min: min,
                        '10%': max * 0.1,
                        '20%': max * 0.2,
                        '30%': max * 0.3,
                        '40%': max * 0.4,
                        '50%': max * 0.5,
                        '60%': max * 0.6,
                        '70%': max * 0.7,
                        '80%': max * 0.8,
                        '90%': max * 0.9,
                        max: max
                    },
                });

                let nodes = [
                    $('.ps-slider__min'),
                    $('.ps-slider__max')
                ];

                element.noUiSlider.on('update', function (values, handle) {
                    nodes[handle].html(number_format(values[handle]));
                });

                element.noUiSlider.on('change', function (values, handle) {
                    $wrapper.find('.product-filter-item-price-' + handle).val(values[handle]).trigger('change');
                });
            });
        }

        filterSlider();

        $(document).on('click', '.js-quick-view-button', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass('button-loading');

            $.ajax({
                url: _self.data('url'),
                type: 'GET',
                success: res => {
                    if (!res.error) {
                        $('#product-quickview .ps-product--quickview').html(res.data);
                        $('.ps-product--quickview .ps-product__images').slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            rtl: isRTL,
                            fade: true,
                            dots: false,
                            arrows: true,
                            infinite: false,
                            prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
                            nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>",
                        });

                        $('#product-quickview').modal('show');
                    }
                    _self.removeClass('button-loading');
                },
                error: () => {
                    _self.removeClass('button-loading');
                }
            });
        });

        $(document).on('click', '.product__qty .up', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let currentVal = parseInt($(this).closest('.product__qty').find('.qty-input').val(), 10);
            $(this).closest('.product__qty').find('.qty-input').val(currentVal + 1).prop('placeholder', currentVal + 1).trigger('change');

            if ($(this).closest('.ps-table--shopping-cart').length) {
                ajaxUpdateCart($(this));
            }
        });

        $(document).on('click', '.product__qty .down', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let currentVal = parseInt($(this).closest('.product__qty').find('.qty-input').val(), 10);
            if (currentVal > 0) {
                $(this).closest('.product__qty').find('.qty-input').val(currentVal - 1).prop('placeholder', currentVal - 1).trigger('change');
            }

            if ($(this).closest('.ps-table--shopping-cart').length) {
                ajaxUpdateCart($(this));
            }
        });

        $(document).on('change', '.product-category-select', function () {
            $('.product-cat-label').text($.trim($(this).find('option:selected').text()));
        });

        $('.product-cat-label').text($.trim($('.product-category-select option:selected').text()));

        let searchTimeout = null;
        $('.input-search-product')
            .on('keydown', function () {
                $(this).closest('form').find('.ps-panel--search-result').html('').removeClass('active');
            })
            .on('keyup', function () {
                const k = $(this).val();
                if (k) {
                    const $form = $(this).closest('form');
                    $form.find('.spinner-icon').show();
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function () {
                        $.ajax({
                            type: 'GET',
                            url: $form.data('ajax-url'),
                            data: $form.serialize(),
                            success: res => {
                                if (!res.error && res.data !== '') {
                                    $form.find('.ps-panel--search-result').html(res.data).addClass('active');
                                } else {
                                    $form.find('.ps-panel--search-result').html('').removeClass('active');
                                }

                                $form.find('.spinner-icon').hide();
                            },
                            error: () => {
                                $form.find('.spinner-icon').hide();
                            }
                        });
                    }, 500);
                }
            });

        $('.rating_wrap > a ').on('click', function (e) {
            e.preventDefault();
            const target = $(this).attr('href');
            $('.ps-tab-list li').removeClass('active');
            $('.ps-tab-list li > a[href="' + target + '"]').closest('li').addClass('active');

            $(target).addClass('active');
            $(target)
                .siblings('.ps-tab')
                .removeClass('active');
            $(target).closest('.ps-tab-root').find('li').removeClass('active');
            $(target).closest('.ps-tab-root').find('li a[href="' + target + '"]').closest('li').addClass('active');

            $('html, body').animate(
                {
                    scrollTop: ($(target).offset().top - $('.header--product .navigation').height() - 165) + 'px',
                },
                800
            );
        });

        $(document).on('click', 'input[name=is_vendor]', function () {
           if ($(this).val() == 1) {
               $('.show-if-vendor').slideDown().show();
           } else {
               $('.show-if-vendor').slideUp();
               setTimeout(function () {
                   $('.show-if-vendor').hide();
               }, 500);
               $(this).closest('form').find('button[type=submit]').prop('disabled', false).removeClass('btn-disabled');
           }
        });

        $('#shop-url')
            .on('keyup', function () {
                let displayURL = $(this).closest('.form-group').find('span small');
                displayURL.html(displayURL.data('base-url') + '/<strong>' + $(this).val().toLowerCase() + '</strong>');
            })
            .on('change', function () {
                $('.shop-url-wrapper').addClass('content-loading');
                $(this).closest('form').find('button[type=submit]').addClass('btn-disabled').prop('disabled', true);

                $.ajax({
                    url: $(this).data('url'),
                    type: 'POST',
                    data: {
                        url: $(this).val(),
                    },
                    success: res =>  {
                        $('.shop-url-wrapper').removeClass('content-loading');
                        if (res.error) {
                            $('.shop-url-status').removeClass('text-success').addClass('text-danger').text(res.message);

                        } else {
                            $('.shop-url-status').removeClass('text-danger').addClass('text-success').text(res.message);
                            $(this).closest('form').find('button[type=submit]').prop('disabled', false).removeClass('btn-disabled');
                        }
                    },
                    error: () =>  {
                        $('.shop-url-wrapper').removeClass('content-loading');
                    }
                });
            });

        if (jQuery().mCustomScrollbar) {
            $('.ps-custom-scrollbar').mCustomScrollbar({
                theme: 'dark',
                scrollInertia: 0
            });
        }
    });

})(jQuery);
