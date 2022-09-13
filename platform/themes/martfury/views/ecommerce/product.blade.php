@php
    Theme::set('stickyHeader', 'false');
    Theme::set('topHeader', Theme::partial('header-product-page', compact('product')));
    Theme::set('bottomFooter', Theme::partial('footer-product-page', compact('product')));
    Theme::set('pageId', 'product-page');
    Theme::set('headerMobile', Theme::partial('header-mobile-product'));
@endphp

<div class="ps-page--product">
    <div class="ps-container">
            <div class="ps-page__container">
                <div class="ps-page__left">
                    <div class="ps-product--detail ps-product--fullwidth">
                        <div class="ps-product__header">
                            <div class="ps-product__thumbnail" data-vertical="true">
                                <figure>
                                    <div class="ps-wrapper">
                                        <div class="ps-product__gallery" data-arrow="true">
                                            @foreach ($productImages as $img)
                                                <div class="item">
                                                    <a href="{{ RvMedia::getImageUrl($img) }}">
                                                        <img src="{{ RvMedia::getImageUrl($img) }}" alt="{{ $product->name }}"/>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </figure>
                                <div class="ps-product__variants" data-item="4" data-md="4" data-sm="4" data-arrow="false">
                                    @foreach ($productImages as $img)
                                        <div class="item">
                                            <img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $product->name }}"/>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="ps-product__info">
                                <h1>{{ $product->name }}</h1>
                                <div class="ps-product__meta">
                                    @if ($product->brand_id)
                                        <p>{{ __('Brand') }}: <a href="{{ $product->brand->url }}">{{ $product->brand->name }}</a></p>
                                    @endif

                                    @if (EcommerceHelper::isReviewEnabled())
                                        @if ($product->reviews_count > 0)
                                            <div class="rating_wrap">
                                                <a href="#tab-reviews">
                                                    <div class="rating">
                                                        <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                                                    </div>
                                                    <span class="rating_num">({{ $product->reviews_count }} {{ __('reviews') }})</span>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <h4 class="ps-product__price @if ($product->front_sale_price !== $product->price) sale @endif"><span>{{ format_price($product->front_sale_price_with_taxes) }}</span> @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price_with_taxes) }} </del> @endif</h4>
                                <div class="ps-product__desc">
                                    @if (is_plugin_active('marketplace') && $product->store_id)
                                        <p>{{ __('Sold By') }}: <a href="{{ $product->store->url }}"><strong>{{ $product->store->name }}</strong></a></p>
                                    @endif

                                    <div class="ps-list--dot">
                                        {!! apply_filters('ecommerce_before_product_description', null, $product) !!}
                                        {!! clean($product->description) !!}
                                        {!! apply_filters('ecommerce_after_product_description', null, $product) !!}
                                    </div>
                                </div>
                                @php $flashSale = $product->latestFlashSales()->first(); @endphp

                                @if ($flashSale)
                                    <div class="ps-product__countdown">
                                        <figure>
                                            <figcaption> {{ __("Don't Miss Out! This promotion will expires in") }}</figcaption>
                                            <ul class="ps-countdown" data-time="{{ $flashSale->end_date }}">
                                                <li><span class="days"></span>
                                                    <p>{{ __('Days') }}</p>
                                                </li>
                                                <li><span class="hours"></span>
                                                    <p>{{ __('Hours') }}</p>
                                                </li>
                                                <li><span class="minutes"></span>
                                                    <p>{{ __('Minutes') }}</p>
                                                </li>
                                                <li><span class="seconds"></span>
                                                    <p>{{ __('Seconds') }}</p>
                                                </li>
                                            </ul>
                                        </figure>
                                        <figure>
                                            <figcaption>{{ __('Sold Items') }}</figcaption>
                                            <div class="ps-product__progress-bar ps-progress" data-value="{{ $flashSale->pivot->quantity > 0 ? ($flashSale->pivot->sold / $flashSale->pivot->quantity) * 100 : 0 }}">
                                                <div class="ps-progress__value"><span style="width: {{ $flashSale->pivot->quantity > 0 ? $flashSale->pivot->sold / $flashSale->pivot->quantity : 0 }}%;"></span></div>
                                                <p><b>{{ $flashSale->pivot->sold }}/{{ $flashSale->pivot->quantity }}</b> {{ __('Sold') }}</p>
                                            </div>
                                        </figure>
                                    </div>
                                @endif

                                @if ($product->variations()->count() > 0)
                                    <div class="pr_switch_wrap">
                                        {!! render_product_swatches($product, [
                                            'selected' => $selectedAttrs,
                                            'view'     => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer'
                                        ]) !!}
                                    </div>
                                    <div class="number-items-available mb-3">
                                        @if ($product->isOutOfStock())
                                            <span class="text-danger">({{ __('Out of stock') }})</span>
                                        @else
                                            @if (!$productVariation)
                                                <span class="text-danger">({{ __('Not available') }})</span>
                                            @else
                                                @if ($productVariation->isOutOfStock())
                                                    <span class="text-danger">({{ __('Out of stock') }})</span>
                                                @elseif  (!$productVariation->with_storehouse_management || $productVariation->quantity < 1)
                                                    <span class="text-success">({{ __('Available') }})</span>
                                                @elseif ($productVariation->quantity)
                                                    <span class="text-success">
                                                        @if ($productVariation->quantity != 1)
                                                            ({{ __(':number products available', ['number' => $productVariation->quantity]) }})
                                                        @else
                                                            ({{ __(':number product available', ['number' => $productVariation->quantity]) }})
                                                        @endif
                                                    </span>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                @endif
                                <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                                    @csrf
                                    {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null) !!}
                                    <div class="ps-product__shopping">
                                        <figure>
                                            <figcaption>{{ __('Quantity') }}</figcaption>
                                            <div class="form-group--number product__qty">
                                                <button class="up" type="button"><i class="icon-plus"></i></button>
                                                <button class="down" type="button"><i class="icon-minus"></i></button>
                                                <input class="form-control qty-input" type="text" name="qty" value="1" placeholder="1" readonly>
                                            </div>
                                        </figure>
                                        <input type="hidden" name="id" class="hidden-product-id" value="{{ ($product->is_variation || !$product->defaultVariation->product_id) ? $product->id : $product->defaultVariation->product_id }}"/>

                                        @if (EcommerceHelper::isCartEnabled())
                                            <button class="ps-btn ps-btn--black @if ($product->isOutOfStock()) btn-disabled @endif" type="submit" @if ($product->isOutOfStock()) disabled @endif>{{ __('Add to cart') }}</button>
                                            @if (EcommerceHelper::isQuickBuyButtonEnabled())
                                                <button class="ps-btn @if ($product->isOutOfStock()) btn-disabled @endif" type="submit" name="checkout" @if ($product->isOutOfStock()) disabled @endif>{{ __('Buy Now') }}</button>
                                            @endif
                                        @endif
                                        <div class="ps-product__actions">
                                            <a class="js-add-to-wishlist-button" href="#" data-url="{{ route('public.wishlist.add', $product->id) }}"><i class="icon-heart"></i></a>
                                            <a class="js-add-to-compare-button" href="#" data-url="{{ route('public.compare.add', $product->id) }}" title="{{ __('Compare') }}"><i class="icon-chart-bars"></i></a>
                                        </div>
                                    </div>
                                </form>
                                <div class="ps-product__specification">

                                    <p @if (!$product->sku) style="display: none" @endif><strong>{{ __('SKU') }}:</strong> <span id="product-sku">{{ $product->sku }}</span></p>
                                    @if ($product->categories->count())
                                        <p class="categories"><strong> {{ __('Categories') }}:</strong>
                                            @foreach($product->categories as $category)
                                                <a href="{{ $category->url }}">{{ $category->name }}</a>@if (!$loop->last),@endif
                                            @endforeach
                                        </p>
                                    @endif

                                    @if ($product->tags->count())
                                        <p class="tags"><strong> {{ __('Tags') }}:</strong>
                                            @foreach($product->tags as $tag)
                                                <a href="{{ $tag->url }}">{{ $tag->name }}</a>@if (!$loop->last),@endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                                <div class="ps-product__sharing">
                                    <a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($product->url) }}" target="_blank"><i class="fa fa-facebook"></i></a>
                                    <a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($product->url) }}&summary={{ rawurldecode(strip_tags($product->description)) }}" target="_blank"><i class="fa fa-linkedin"></i></a>
                                    <a class="twitter" href="https://twitter.com/intent/tweet?url={{ urlencode($product->url) }}&text={{ strip_tags($product->description) }}" target="_blank"><i class="fa fa-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="ps-product__content ps-tab-root">
                            <ul class="ps-tab-list">
                                <li class="active"><a href="#tab-description">{{ __('Description') }}</a></li>
                                @if (EcommerceHelper::isReviewEnabled())
                                    <li><a href="#tab-reviews">{{ __('Reviews') }} ({{ $product->reviews_count }})</a></li>
                                @endif
                                @if (is_plugin_active('marketplace') && $product->store_id)
                                    <li><a href="#tab-vendor">{{ __('Vendor') }}</a></li>
                                @endif
                                @if (is_plugin_active('faq'))
                                    @if (count($product->faq_items) > 0)
                                        <li><a href="#tab-faq">{{ __('Questions and Answers') }}</a></li>
                                    @endif
                                @endif
                            </ul>
                            <div class="ps-tabs">
                                <div class="ps-tab active" id="tab-description">
                                    <div class="ps-document">
                                        <div>
                                            {!! clean($product->content) !!}
                                        </div>
                                        @if (theme_option('facebook_comment_enabled_in_product', 'yes') == 'yes')
                                            <br />
                                            {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, Theme::partial('comments')) !!}
                                        @endif
                                    </div>
                                </div>
                                @if (EcommerceHelper::isReviewEnabled())
                                    <div class="ps-tab" id="tab-reviews">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="ps-block--average-rating">
                                                    <div class="ps-block__header">
                                                        <h3>{{ number_format($product->reviews_avg, 2) }}</h3>
                                                        @if ($product->reviews_count > 0)
                                                            <div class="rating_wrap">
                                                                <div class="rating">
                                                                    <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                                                                </div>
                                                                <span class="rating_num">({{ $product->reviews_count }} {{ __('reviews') }})</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @foreach (EcommerceHelper::getReviewsGroupedByProductId($product->id, $product->reviews_count) as $item)
                                                        <div class="ps-block__star @if (!$item['count']) disabled @endif" data-star="{{ $item['star'] }}">
                                                            <span>{{ __(':star Star', ['star' => $item['star']]) }}</span>
                                                            <div class="ps-progress" data-value="{{ $item['count'] }}">
                                                                <span></span>
                                                            </div>
                                                            <span>{{ $item['percent'] }}%</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-lg-7">
                                                {!! Form::open([
                                                        'route'  => 'public.reviews.create',
                                                        'method' => 'POST',
                                                        'class'  => 'ps-form--review form-review-product',
                                                        'files'  => true,
                                                    ]) !!}
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <h4>{{ __('Submit Your Review') }}</h4>
                                                    @if (!auth('customer')->check())
                                                        <p class="text-danger">{{ __('Please') }} <a href="{{ route('customer.login') }}">{{ __('login') }}</a> {{ __('to write review!') }}</p>
                                                    @endif
                                                    <div class="form-group form-group__rating">
                                                        <label for="review-star">{{ __('Your rating of this product') }}</label>
                                                        <select name="star" class="ps-rating" data-read-only="false" id="review-star">
                                                            <option value="0">0</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="comment" id="txt-comment" rows="6" placeholder="{{ __('Write your review') }}" @if (!auth('customer')->check()) disabled @endif></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <script type="text/x-custom-template" id="review-image-template">
                                                            <span class="image-viewer__item" data-id="__id__">
                                                                <img src="{{ RvMedia::getDefaultImage() }}" alt="Preview" class="img-responsive d-block">
                                                                <span class="image-viewer__icon-remove">
                                                                    <i class="icon-cross-circle"></i>
                                                                </span>
                                                            </span>
                                                        </script>
                                                        <div class="image-upload__viewer d-flex">
                                                            <div class="image-viewer__list position-relative">
                                                                <div class="image-upload__uploader-container">
                                                                    <div class="d-table">
                                                                        <div class="image-upload__uploader">
                                                                            <i class="fa fa-image image-upload__icon"></i>
                                                                            <div class="image-upload__text">{{ __('Upload photos') }}</div>
                                                                            <input type="file"
                                                                                name="images[]"
                                                                                data-max-files="{{ EcommerceHelper::reviewMaxFileNumber() }}"
                                                                                class="image-upload__file-input"
                                                                                accept="image/png,image/jpeg,image/jpg"
                                                                                multiple="multiple"
                                                                                data-max-size="{{ EcommerceHelper::reviewMaxFileSize(true) }}"
                                                                                data-max-size-message="{{ trans('validation.max.file', ['attribute' => '__attribute__', 'max' => '__max__']) }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="loading">
                                                                    <div class="half-circle-spinner">
                                                                        <div class="circle circle-1"></div>
                                                                        <div class="circle circle-2"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="help-block">
                                                            {{ __('You can upload up to :total photos, each photo maximum size is :max kilobytes', [
                                                                    'total' => EcommerceHelper::reviewMaxFileNumber(),
                                                                    'max'   => EcommerceHelper::reviewMaxFileSize(true),
                                                                ]) }}
                                                        </div>

                                                    </div>

                                                    <div class="form-group submit">
                                                        <button class="ps-btn @if (!auth('customer')->check()) btn-disabled @endif" type="submit" @if (!auth('customer')->check()) disabled @endif>{{ __('Submit Review') }}</button>
                                                    </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>

                                        @if ($product->reviews_count)
                                            @if (count($product->review_images))
                                                <div class="my-3">
                                                    <h4>{{ __('Images from customer') . ' (' . count($product->review_images) . ')' }}</h4>
                                                    <div class="block--review">
                                                        <div class="block__images row m-0 block__images_total">
                                                            @foreach ($product->review_images as $img)
                                                                <a href="{{ RvMedia::getImageUrl($img) }}" class="col-lg-1 col-sm-2 col-3 more-review-images @if ($loop->iteration > 12) d-none @endif">
                                                                    <div class="border position-relative rounded">
                                                                        <img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $product->name }}" class="img-responsive rounded h-100">
                                                                        @if ($loop->iteration == 12 && (count($product->review_images) - $loop->iteration > 0))
                                                                            <span>+{{ count($product->review_images) - $loop->iteration }}</span>
                                                                        @endif
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="block--product-reviews">
                                                        <div class="block__header d-md-flex align-items-center justify-content-between">
                                                            <div class="pb-4 pb-md-0">
                                                                <h2>{{ __(':total review(s) for ":product"', [
                                                                        'total'   => $product->reviews_count,
                                                                        'product' => $product->name,
                                                                    ]) }}
                                                                </h2>
                                                            </div>
                                                            <div class="ps-review__filter-by-star d-flex align-items-center justify-content-end">
                                                                <div class="px-2 d-flex align-items-center">
                                                                    <i class="icon-funnel"></i>
                                                                    <span>{{ __('Filter') }}:</span>
                                                                </div>
                                                                <div class="ps-review__filter-select">
                                                                    <select class="ps-select">
                                                                        <option value="0">{{ __('All Star') }}</option>
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <option value="{{ $i }}">{{ __(':star Star', ['star' => $i]) }}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="block__content" id="app">
                                                            <product-reviews-component url="{{ route('public.ajax.product-reviews', $product->id) }}"></product-reviews-component>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if (is_plugin_active('marketplace') && $product->store_id)
                                    <div class="ps-tab" id="tab-vendor">
                                        <h4>{{ $product->store->name }}</h4>
                                        <div>
                                            {!! clean($product->store->content) !!}
                                        </div>

                                        <a href="{{ $product->store->url }}" class="more-products">
                                            {{ __('More Products from :store',  ['store' => $product->store->name]) }}
                                        </a>
                                    </div>
                                @endif

                                @if (is_plugin_active('faq') && count($product->faq_items) > 0)
                                    <div class="ps-tab" id="tab-faq">
                                        <div class="accordion" id="faq-accordion">
                                            @foreach($product->faq_items as $faq)
                                                <div class="card">
                                                    <div class="card-header" id="heading-faq-{{ $loop->index }}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left @if (!$loop->first) collapsed @endif" type="button" data-toggle="collapse" data-target="#collapse-faq-{{ $loop->index }}" aria-expanded="true" aria-controls="collapse-faq-{{ $loop->index }}">
                                                                {!! clean($faq[0]['value']) !!}
                                                            </button>
                                                        </h2>
                                                    </div>

                                                    <div id="collapse-faq-{{ $loop->index }}" class="collapse @if ($loop->first) show @endif" aria-labelledby="heading-faq-{{ $loop->index }}" data-parent="#faq-accordion">
                                                        <div class="card-body">
                                                            {!! clean($faq[1]['value']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ps-page__right">
                    <aside class="widget widget_product widget_features">
                        @for ($i = 1; $i <= 5; $i++)
                            @if (theme_option('product_feature_' . $i . '_title'))
                                <p><i class="{{ theme_option('product_feature_' . $i . '_icon') }}"></i> {{ theme_option('product_feature_' . $i . '_title') }}</p>
                            @endif
                        @endfor
                    </aside>
                    @if (is_plugin_active('ads'))
                        <aside class="widget">
                            {!! AdsManager::display('product-sidebar') !!}
                        </aside>
                    @endif
                </div>
            </div>

            @php
                $crossSellProducts = get_cross_sale_products($product, 7);
            @endphp
            @if (count($crossSellProducts) > 0)
                {!! Theme::partial('cross-sell-products', compact('crossSellProducts')) !!}
            @endif

            <div class="ps-section--default" id="products">
                <div class="ps-section__header">
                    <h3>{{ __('Related products') }}</h3>
                </div>
                <related-products-component url="{{ route('public.ajax.related-products', $product->id) }}?limit=6"></related-products-component>
            </div>
    </div>
</div>
