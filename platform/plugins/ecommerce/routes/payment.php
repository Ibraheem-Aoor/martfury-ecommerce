<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers\Fronts', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => 'payment'], function () {
        Route::get('status/{paymenttype}', 'PublicCheckoutController@payPalPaymentService')->name('public.payment.paypal.status');
        Route::get('validate-form', 'PublicCheckoutController@validateCheckoutForm')->name('public.payment.validate-checkout-form');
    });
});
