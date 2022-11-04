<?php

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Table\Http\Controllers\TableController;
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
