<?php
namespace App\Http\Controllers\Api\v1;

use App\Services\Api\ApiService;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Media\Facades\RvMediaFacade;
use Botble\Media\RvMedia;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Throwable;
use Yajra\DataTables\Exceptions\Exception;
use Botble\Slug\Facades\SlugHelperFacade;
use Botble\Slug\Models\Slug;
use Str;

class ProductController extends Controller
{

    protected $api;
    public function __construct()
    {
        $base_url = config('bol-control-api.base_url');
        $token = config('bol-control-api.token');
        $this->api = new ApiService($base_url , $token);
    }



    public function getBolControlProductsAndStore()
    {
        ini_set('max_execution_time' , 340);
        try{
            $endpoint = 'product';
            $result = $this->api->get($endpoint);
            if($result['status'])
            {
                $last_page = $result['last_page'];
                $first_round_products = $result['products'];
                $this->storeBolControlProducts($first_round_products);
                for($i=1;$i < $last_page ; $i++)
                {
                    $endpoint = 'product';
                    $products = $this->api->get($endpoint , ['page' => $i+1])['products'];
                    $this->storeBolControlProducts($products);
                }
                info('GG For Bol-Control Products');
            }else{
                throw new Exception();
            }
        }catch(Throwable $e)
        {
            info($e);
            // dd($e);
        }
    }

    /**
     * Change name to long text
     */




    public function storeBolControlProducts($products)
    {
        foreach($products as $product)
        {
            if(!(Product::query()->where('ean_code' , $product['ean'])->exists()) )
            {
            $product['wide'] = $product['width'];
            $product['attr_weight']  = $product['weight'];
            $product['attr_height']  = $product['height'];
            $product['attr_width']  = $product['width'];
            $product['attr_length']  = $product['length'];
            $product['name'] = $product['title'];
            $product['ean_code'] = $product['ean'];
            // $product['images'] =  json_encode([str_replace(RvMediaFacade::getUploadURL() . '/', '', trim($product['image_url']))]);
            $product['status'] = BaseStatusEnum::PENDING;
            try{
                DB::beginTransaction();
                $created_product = Product::create($product);
                Slug::create([
                    'key' => Str::slug(Str::limit($created_product->name,120, '...')),
                    'prefix' => SlugHelperFacade::getPrefix(Product::class),
                    'reference_type' => Product::class,
                    'reference_id' => $created_product->id,
                ]);
                DB::commit();
            }catch(QueryException $e)
            {
                if($e->errorInfo[1] == 1062)
                {
                    // $this->updateProduct($product);
                }else{
                    info($e);
                }
            // info($e);

            }catch(Throwable $e)
            {
                info($e);
                // dd($e);
            }
        }

    }
    }


    public function updateProduct($product_array)
    {
        try{
            $product = Product::query()->where('ean_code' , $product_array['ean_code'])->first();
            $old_staus = $product->status;
            $product->update($product_array);
            $product->status = $old_staus;
            $product->save();
            if(!$product->slug)
            {
                Slug::create([
                    'key' => Str::slug(Str::limit($product->name,120, '...')),
                    'prefix' => SlugHelperFacade::getPrefix(Product::class),
                    'reference_type' => Product::class,
                    'reference_id' => $product->id,
                ]);
            }
        }catch(QueryException $e)
        {
            if($e->errorInfo[1] == 1062)
            {
                $this->updateProduct($product);
            }else{
                info($e);
            }
            // dd($e);
        }catch(Throwable $e)
        {
            info($e);
            // dd($e);
        }
    }


    public function transTest()
    {
        // Product::query()->chunk(200 , function($products)
        // {
        //     foreach($products as $product)
        //     {
        //         try{

        //             $product->weight =  number_format((double)($product->weight  * 1000) , 0 ,2);
        //             $product->save();
        //         }catch(Throwable $e)
        //         {
        //             dd($e);
        //         }
        //     }
        // });

        ini_set('max_execution_time' , 900);
        $without_desc_count = Product::query()->whereHas('translations' , function($trans){
            $trans->whereNull('description');
        })->pluck('ean_code');
        $without_content_count = Product::query()->whereHas('translations' , function($trans){
            $trans->whereNull('content');
        })->pluck('ean_code');
        $result = $without_content_count->merge($without_desc_count);
        dd($result->unique());
        // dd(Product::query()->whereDoesntHave('translations')->count());
        // dd('Done');
    }



}
