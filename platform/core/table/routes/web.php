<?php

use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\FixerControlle;
use App\Models\WpAddress;
use App\Models\WpOrder;
use App\Models\WpUser;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTranslation;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Slug\Models\Slug;
use Botble\Table\Http\Controllers\TableController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Stichoza\GoogleTranslate\GoogleTranslate;


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



Route::get('update-recent-orders', function () {
    $orders = Order::query()->where('created_at'  , ">=",  Carbon::yesterday()->toDateTimeString())->get();
    foreach($orders as $order)
    {
        $order->payment->update(['status' => PaymentStatusEnum::COMPLETED]);
    }
    dd($order->count());
});

#7548746
Route::get('solve-qty', function () {
    Product::query()->where('quantity', 1)->update(['quantity' => 0]);
    dd('Done');
});


Route::get('tttt', function () {
    // $p = Product::find(8711527);
    // $tr = new GoogleTranslate('ar');
    // // $p->translations()->whereLangCode('en_US')->first()->update([
    // //     'name' => $tr->translate($p->name),
    // //     'content' => $tr->translate($p->content),
    // //     'description' => $tr->translate($p->description),
    // // ]);
    // dd($tr->translate('Hello World'));

    $trs = ProductTranslation::query()->whereEcProductsId(8711528)->get();
    dd($trs);
});



/**
 * Quick fixing routes
 */


Route::get('products-without-trans-test',  [FixerControlle::class , 'getProductsWithoutTrans']);
Route::get('feature-all-products', [FixerControlle::class, 'featureAllProducts']);
Route::get('get-eans-duplicates' , [FixerControlle::class , 'getEansDuplicates']);


Route::get('bol-images-download', [FixerControlle::class, 'donwloadBolImagesInStorage']);


Route::get('get-dups-slug', [FixerControlle::class, 'getDuplicatedSlugs']);
Route::get('paynyl-test', [FixerControlle::class, 'testPaybl'])->name('pay-test');


Route::get('customer-fix', [FixerControlle::class, 'getCstomr']);

Route::get('proccessing-orders-count', function () {
    dd(Order::query()->whereStatus(OrderStatusEnum::PROCESSING)->count());
});

Route::get('clean-products', function () {
    $products = Product::all();
    $arr = [];
    foreach($products as $product)
    {
        if(!is_numeric($product->ean_code))
        {
            array_push($arr, $product);
        }
    }
    dd($arr);
});
Route::get('clean-products-delete', function () {
    $products = Product::all();
    $arr = [];
    foreach($products as $product)
    {
        if(!is_numeric($product->ean_code))
        {
            array_push($arr, $product->ean_code);
        }
    }
    Product::query()->whereIn('ean_code' , $arr)->delete();
    dd('DELETED');
});

Route::get('customer-with-house-no', function () {
    $customers = Customer::query()->whereHas(
        'addresses',
        function ($addresses) {
            $addresses->whereNotNull('house_no');
    }
    )->count();
    dd($customers);
});

Route::get('order-fix', function () {
    $orders =  [
    119 => "20",
    114 => "2020",
    109 => "2",
    145 => "26",
    147 => "2a",
    148 => "33",
    150 => "85",
    149 => "12",
    151 => "95",
    152 => "11",
    153 => "93",
    155 => "19",
    154 => "15 A",
    158 => "28",
    161 => "22",
    160 => "163",
    162 => "19",
    164 => "18",
    165 => "2",
    166 => "1",
    167 => "2th",
    168 => "23",
    171 => "1th",
    172 => "2",
    174 => "5",
    175 => "8",
    185 => "2323",
    198 => "test",
    199 => "1223",
    201 => "23",
    202 => "2323",
    203 => "2",
    214 => "50",
    219 => "2a",
    221 => "2a"];

    foreach($orders as $order_id => $house_no)
    {
        try{
            $order = Order::find($order_id);
            $order?->user->addresses()->update(['house_no' => $house_no]);
        }catch(Throwable $e)
        {
            dd($e);
        }
    }
    dd('Updated Addresses');
});


Route::get('customer-withorder-no-address', function () {
    $customers = Customer::whereHas('order')->whereHas('addresses', function ($address) {
        $address->wereNull('house_no');
    }
    )->pluck('name' , 'id');
});




Route::get('wp-trans-orders', function () {
    Order::query()->chunk(50, function ($orders) {
        foreach($orders as $order)
        {
            WpOrder::create([
                'order_id' => $order->id,
                'parent_id' => 0,
                'date_created' => Carbon::now()->toDateTimeString(),
                'date_created_gmt' => Carbon::now()->toDateTimeString(),
                'total_sales' => $order->products->sum('qty'),
                'tax_total' => 0,
                'shipping_total' => $order->shipping_amount,
                'net_total' => $order->amount,
                'status' => 'completed' ,
                'customer_id' => $order->user->id,
            ]);
        }
    });
    dd('Done');
});


Route::get('wp-trans-cusomter', function () {
    Customer::query()->chunk(50, function ($customers) {
        foreach($customers as $customer)
        {
            WpUser::create([
                'user_login' => $customer->name,
                'user_pass' => Hash::make('123456'),
                'user_nicename' => $customer->name,
                'user_email' => $customer->email,
                'user_url' => 'https://borvat.com',
                'user_registered' => Carbon::now()->toDateTimeString(),
                'user_activation_key' => '123456',
                'user_status' => 0,
                'display_name' => $customer->name,
            ]);
        }
    });
    dd('Done');
});



Route::get('wp-trans-cusomter-address', function () {
    @ini_set('max_execution_time', -1);
    @ini_set('memory_limit', -1);
    Customer::query()->whereHas('addresses')->chunk(50, function ($customers) {
        foreach($customers as $customer)
        {
            $user_id = WpUser::query()->whereUserEmail($customer->email)->first()?->ID;
            $address = $customer->addresses->first();
            $full_name = explode(' ' , $address->name);
            $first_name = $full_name[0];
            $last_name = implode(' ', $full_name);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_first_name',
                'meta_value' => $first_name,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_last_name',
                'meta_value' => $last_name,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_address_1',
                'meta_value' => $address->address,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_city',
                'meta_value' => $address->city,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_postcode',
                'meta_value' => $address->zip_code,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_country',
                'meta_value' => $address->country,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_wooccm9',
                'meta_value' => $address->email,
            ]);
            WpAddress::create([
                'user_id' => $user_id,
                'meta_key' => 'shipping_phone',
                'meta_value' => $address->phone,
            ]);
        }
    });
    dd('DONE');
});





