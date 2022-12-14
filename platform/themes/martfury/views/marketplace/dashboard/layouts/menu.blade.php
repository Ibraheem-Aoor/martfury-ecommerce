@php
    $menus = collect([
        [
            'key'   => 'marketplace.vendor.dashboard',
            'icon'  => 'icon-home',
            'name'  => __('Dashboard'),
            'order' => 1,
        ],
        [
            'key'    => 'marketplace.vendor.products.index',
            'icon'   => 'icon-database',
            'name'   => __('Products'),
            'routes' => [
                'marketplace.vendor.products.create',
                'marketplace.vendor.products.edit',
            ],
            'order' => 2,
        ],
        [
            'key'    => 'marketplace.vendor.products.bulk-import.index',
            'icon'   => 'fas fa-file-import',
            'name'   => __('Import'),
            'routes' => [
                'marketplace.vendor.products.bulk-import.index',
            ],
            'order' => 9,
        ],
        // [
        //     'key'    => 'marketplace.vendor.product-attribute-sets.index',
        //     'icon'   => 'fas fa-glass-martini',
        //     'name'   => __('Product Attributes'),
        //     'routes' => [
        //         'marketplace.vendor.product-attribute-sets.create',
        //         'marketplace.vendor.product-attribute-sets.edit',
        //     ],
        //     'order' => 3,
        // ],
        [
            'key'    => 'marketplace.vendor.brands.index',
            'icon'   => 'fa fa-registered',
            'name'   => __('Brands'),
            'routes' => [
                'marketplace.vendor.brands.create',
                'marketplace.vendor.brands.edit',
            ],
            'order' => 4,
        ],
        [
            'key'    => 'marketplace.vendor.orders.index',
            'icon'   => 'icon-bag2',
            'name'   => __('Orders'),
            'routes' => [
                'marketplace.vendor.orders.edit',
            ],
            'order' => 5,
        ],
        [
            'key'    => 'marketplace.vendor.discounts.index',
            'icon'   => 'icon-gift',
            'name'   => __('Coupons'),
            'routes' => [
                'marketplace.vendor.discounts.create',
                'marketplace.vendor.discounts.edit',
            ],
            'order' => 6,
        ],
        [
            'key'    => 'marketplace.vendor.withdrawals.index',
            'icon'   => 'icon-bag-dollar',
            'name'   => __('Withdrawals'),
            'routes' => [
                'marketplace.vendor.withdrawals.create',
                'marketplace.vendor.withdrawals.edit',
            ],
            'order' => 7,
        ],
        [
            'key'   => 'marketplace.vendor.settings',
            'icon'  => 'icon-cog',
            'name'  => __('Settings'),
            'order' => 8,
        ],
        [
            'key'   => 'customer.overview',
            'icon'  => 'icon-user',
            'name'  => __('Customer dashboard'),
            'order' => 9,
        ],
    ]);

    if (EcommerceHelper::isReviewEnabled()) {
        $menus->push([
            'key'   => 'marketplace.vendor.reviews.index',
            'icon'  => 'icon-star',
            'name'  => __('Reviews'),
            'order' => 5,
        ]);
    }

    $currentRouteName = Route::currentRouteName();
@endphp

<ul class="menu">
    @foreach ($menus->sortBy('order') as $item)
        <li>
            <a @if ($currentRouteName == $item['key'] || in_array($currentRouteName, Arr::get($item, 'routes', []))) class="active" @endif href="{{ route($item['key']) }}">
                <i class="{{ $item['icon'] }}"></i>{{ $item['name'] }}
            </a>
        </li>
    @endforeach

</ul>
{{-- <li>
    <a href="#" class="btn-sm btn-" onclick="event.preventDefault();$('#new_brand_modal').modal('show');"><i class="fa fa-registered"></i> {{__('Brands')}}</a>
</li> --}}
