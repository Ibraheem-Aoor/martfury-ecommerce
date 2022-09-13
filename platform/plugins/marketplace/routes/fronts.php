<?php

use Botble\Marketplace\Models\Store;

Route::group([
    'namespace'  => 'Botble\Marketplace\Http\Controllers\Fronts',
    'middleware' => ['web', 'core'],
], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get(SlugHelper::getPrefix(Store::class, 'stores'), [
            'as'   => 'public.stores',
            'uses' => 'PublicStoreController@getStores',
        ]);

        Route::get(SlugHelper::getPrefix(Store::class, 'stores') . '/{slug}', [
            'uses' => 'PublicStoreController@getStore',
            'as'   => 'public.store',
        ]);

        Route::post('ajax/stores/check-store-url', [
            'as'   => 'public.ajax.check-store-url',
            'uses' => 'PublicStoreController@checkStoreUrl',
        ]);

        Route::group([
            'prefix'     => 'vendor',
            'as'         => 'marketplace.vendor.',
            'middleware' => ['vendor'],
        ], function () {

            Route::group(['prefix' => 'ajax'], function () {
                Route::post('upload', [
                    'as'   => 'upload',
                    'uses' => 'DashboardController@postUpload',
                ]);

                Route::post('upload-from-editor', [
                    'as'   => 'upload-from-editor',
                    'uses' => 'DashboardController@postUploadFromEditor',
                ]);

                Route::group(['prefix' => 'chart', 'as' => 'chart.'], function () {
                    Route::get('month', [
                        'as'   => 'month',
                        'uses' => 'RevenueController@getMonthChart',
                    ]);
                });
            });

            Route::get('dashboard', [
                'as'   => 'dashboard',
                'uses' => 'DashboardController@index',
            ]);

            Route::get('settings', [
                'as'   => 'settings',
                'uses' => 'SettingController@index',
            ]);

            Route::post('settings', [
                'as'   => 'settings.post',
                'uses' => 'SettingController@saveSettings',
            ]);

            Route::resource('revenues', 'RevenueController')
                ->parameters(['' => 'revenue'])
                ->only(['index']);

            Route::resource('withdrawals', 'WithdrawalController')
                ->parameters(['' => 'withdrawal'])
                ->only([
                    'index',
                    'create',
                    'store',
                    'edit',
                    'update',
                ]);

            Route::group(['prefix' => 'withdrawals'], function () {
                Route::get('show/{id}', [
                    'as'   => 'withdrawals.show',
                    'uses' => 'WithdrawalController@show',
                ]);
            });

            if (EcommerceHelper::isReviewEnabled()) {
                Route::resource('reviews', 'ReviewController')
                    ->parameters(['' => 'review'])
                    ->only(['index']);
            }

            Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
                Route::resource('', 'ProductController')
                    ->parameters(['' => 'product']);
            Route::post('product-ean-check-vendor' ,'ProductController@isProductEanCodeExists' )->name('ean_check_vendor');


                Route::delete('items/destroy', [
                    'as'   => 'deletes',
                    'uses' => 'ProductController@deletes',
                ]);

                Route::post('add-attribute-to-product/{id}', [
                    'as'   => 'add-attribute-to-product',
                    'uses' => 'ProductController@postAddAttributeToProduct',
                ]);

                Route::post('delete-version/{id}', [
                    'as'   => 'delete-version',
                    'uses' => 'ProductController@deleteVersion',
                ]);

                Route::post('add-version/{id}', [
                    'as'   => 'add-version',
                    'uses' => 'ProductController@postAddVersion',
                ]);

                Route::get('get-version-form/{id?}', [
                    'as'   => 'get-version-form',
                    'uses' => 'ProductController@getVersionForm',
                ]);

                Route::post('update-version/{id}', [
                    'as'   => 'update-version',
                    'uses' => 'ProductController@postUpdateVersion',
                ]);

                Route::post('generate-all-version/{id}', [
                    'as'   => 'generate-all-versions',
                    'uses' => 'ProductController@postGenerateAllVersions',
                ]);

                Route::post('store-related-attributes/{id}', [
                    'as'   => 'store-related-attributes',
                    'uses' => 'ProductController@postStoreRelatedAttributes',
                ]);

                Route::post('save-all-version/{id}', [
                    'as'   => 'save-all-versions',
                    'uses' => 'ProductController@postSaveAllVersions',
                ]);

                Route::get('get-list-product-for-search', [
                    'as'   => 'get-list-product-for-search',
                    'uses' => 'ProductController@getListProductForSearch',
                ]);

                Route::get('get-relations-box/{id?}', [
                    'as'   => 'get-relations-boxes',
                    'uses' => 'ProductController@getRelationBoxes',
                ]);

                Route::get('get-list-products-for-select', [
                    'as'   => 'get-list-products-for-select',
                    'uses' => 'ProductController@getListProductForSelect',
                ]);

                Route::post('create-product-when-creating-order', [
                    'as'   => 'create-product-when-creating-order',
                    'uses' => 'ProductController@postCreateProductWhenCreatingOrder',
                ]);

                Route::get('get-all-products-and-variations', [
                    'as'   => 'get-all-products-and-variations',
                    'uses' => 'ProductController@getAllProductAndVariations',
                ]);

                Route::post('update-order-by', [
                    'as'   => 'update-order-by',
                    'uses' => 'ProductController@postUpdateOrderby',
                ]);
            });

            Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
                Route::resource('', 'OrderController')->parameters(['' => 'order'])->except(['create', 'store']);

                Route::delete('items/destroy', [
                    'as'   => 'deletes',
                    'uses' => 'OrderController@deletes',
                ]);

                Route::get('generate-invoice/{id}', [
                    'as'   => 'generate-invoice',
                    'uses' => 'OrderController@getGenerateInvoice',
                ]);

                Route::post('confirm', [
                    'as'   => 'confirm',
                    'uses' => 'OrderController@postConfirm',
                ]);

                Route::post('send-order-confirmation-email/{id}', [
                    'as'   => 'send-order-confirmation-email',
                    'uses' => 'OrderController@postResendOrderConfirmationEmail',
                ]);

                Route::post('update-shipping-address/{id}', [
                    'as'   => 'update-shipping-address',
                    'uses' => 'OrderController@postUpdateShippingAddress',
                ]);

                Route::post('cancel-order/{id}', [
                    'as'   => 'cancel',
                    'uses' => 'OrderController@postCancelOrder',
                ]);
            });

            Route::group(['prefix' => 'coupons', 'as' => 'discounts.'], function () {
                Route::resource('', 'DiscountController')->parameters(['' => 'coupon'])->except(['edit', 'update']);

                Route::delete('items/destroy', [
                    'as'         => 'deletes',
                    'uses'       => 'DiscountController@deletes',
                ]);

                Route::post('generate-coupon', [
                    'as'         => 'generate-coupon',
                    'uses'       => 'DiscountController@postGenerateCoupon',
                ]);
            });
        });

        Route::group([
            'prefix'     => 'vendor',
            'as'         => 'marketplace.vendor.',
            'middleware' => ['customer'],
        ], function () {

            Route::get('become-vendor', [
                'as'   => 'become-vendor',
                'uses' => 'DashboardController@getBecomeVendor',
            ]);

            Route::post('become-vendor', [
                'as'   => 'become-vendor.post',
                'uses' => 'DashboardController@postBecomeVendor',
            ]);

        });
    });
});

Route::group([
    'namespace'  => 'Botble\Ecommerce\Http\Controllers',
    'middleware' => ['web', 'core'],
], function () {
    Route::group([
        'prefix'     => 'vendor',
        'as'         => 'marketplace.vendor.',
        'middleware' => ['vendor'],
    ], function () {
        Route::get('tags/all', [
            'as'   => 'tags.all',
            'uses' => 'ProductTagController@getAllTags',
        ]);
    });
});

Route::group([
    'namespace'  => 'Botble\Marketplace\Http\Controllers\Fronts',
    'middleware' => ['web', 'core'],
], function () {
    Route::group([
        'prefix'     => 'vendor',
        'as'         => 'marketplace.vendor.',
        'middleware' => ['vendor'],
    ], function () {

        Route::post('language-advanced/save/{id}', [
            'as'   => 'language-advanced.save',
            'uses' => 'DashboardController@save',
        ]);
    });
});
