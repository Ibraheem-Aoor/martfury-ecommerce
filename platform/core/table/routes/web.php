<?php

use App\Http\Controllers\Api\v1\ProductController;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Slug\Models\Slug;
use Botble\Table\Http\Controllers\TableController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => ['web', 'core', 'auth'],
    'prefix'     => BaseHelper::getAdminPrefix() . '/tables',
    'permission' => false,
], function () {
    Route::get('bulk-change/data', [TableController::class, 'getDataForBulkChanges'])->name('tables.bulk-change.data');
    Route::post('bulk-change/save', [TableController::class, 'postSaveBulkChange'])->name('tables.bulk-change.save');
    Route::get('get-filter-input', [TableController::class, 'getFilterInput'])->name('tables.get-filter-input');

});

Route::get('test' , function()
{
    dd(Order::with(['shipment' , 'address' , 'products'])->first()->payment);
        // $orders = Order::query()->whereHas('products')->get();
        // foreach($orders as $order)
        // {
        //     if($order->products->count() > 1)
        //         dd($order);
        // }

});


Route::get('test-api' , [ProductController::class , 'getBolControlProductsAndStore']);

Route::get('make-fake-ean' , function()
{
    $products = Product::query()->get();
    foreach($products as $product)
    {
        $product->ean_code = generateEanCode();
        $product->save();
    }

    $eans = Product::query()->pluck('ean_code');
    dd($eans);
});


function generateEanCode()
{
    $unique_ean_code = mt_rand(10000 , 20000);
    if(Product::whereEanCode($unique_ean_code)->exists())
        generateEanCode();
    else return $unique_ean_code;
}



Route::get('delete-products-slugs' , function()
{

    Slug::query()->wherePrefix('products')->delete();
    dd(Slug::query()->wherePrefix('products')->count()
);
});


Route::get('test-order' , function()
{

    dd(Order::with('shipment')->find(77)->getShippingMethodNameAttribute());
    // dd(  $orders = Order::query()
    // ->whereHas('payment' , function($payment)
    // {
    //     $payment->where('status' , PaymentStatusEnum::COMPLETED);
    // })
    // ->with([
    //     'shipment' ,
    //     'products' ,
    //     'address' ,
    //     ])->get());
});

Route::get('ss' , [ProductController::class , 'importProducts']);
Route::get('disable-products-without-price' , [ProductController::class , 'disableProductsWithoutPrice']);
Route::get('update-slug' , [ProductController::class , 'updateProductsSlug']);
Route::get('get-products-with-image-no-price' , [ProductController::class , 'getProductsWithImageAndWithoutPrice']);
Route::get('update-products-without-price' , [ProductController::class , 'updateProductsWithoutPrice']);
Route::get('update-products-trans' ,[ProductController::class , 'updatePublishedProductsTranslations'] );
Route::get('get-products-without-disc' ,[ProductController::class , 'getProductsWithoutDiscAttr'] );
// Route::get('update-products-without-price' , [ProductController::class , 'upget-products-without-disc' ,[ProductController::class , 'getProductsWidateProductsWithoutPrice']);
Route::get('update-products-translations' , [ProductController::class , 'updateProductsTranslations'] );
Route::get('update-borvat-code' ,[ProductController::class , 'updateBorvatCode'] );


Route::get('count-test', function () {
    $products_to_delete = Product::whereNull('ean_code')->pluck('id');
    dd(Product::query()->whereIn('id', $products_to_delete)->delete());
});

