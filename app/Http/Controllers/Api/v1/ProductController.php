<?php
namespace App\Http\Controllers\Api\v1;

use App\Services\Api\ApiService;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTranslation;
use Botble\Media\Facades\RvMediaFacade;
use Botble\Media\RvMedia;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Throwable;
use Yajra\DataTables\Exceptions\Exception;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Botble\Slug\Facades\SlugHelperFacade;
use Stichoza\GoogleTranslate\GoogleTranslate;



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


    public function importExcelProducts()
    {

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
        Excel::create('borvat-products', function($excel) use($result){

            $excel->sheet('products', function($sheet)use($result){

                $sheet->fromArray($result);

            });

        })->export('xls');
        // dd(Product::query()->whereDoesntHave('translations')->count());
        // dd('Done');
    }



    public function  importProducts()
    {
        $no_content = ProductTranslation::whereNull('content')->count();
        $no_desc = ProductTranslation::whereNull('description')->count();
        $no_price = Product::where('price' , 0)->orWhereNull('price')->count();
        dd($no_content , $no_desc , $no_price);

    }



    public function disableProductsWithoutPrice()
    {
        Product::query()->whereNull('ean_code')->orWhere('price' , 0)->orWhere('price' , null)->update([
            'status' => BaseStatusEnum::PENDING,
        ]);
        dd('Done Successfully');
    }


    public function updateProductsSlug()
    {
        @ini_set('max_execution_time', -1);
        @ini_set('memory_limit', -1);
        Slug::query()->whereReferenceType(Product::class)->delete();
        Product::query()->chunk(200 , function($products){
            foreach($products as $product)
            {
                $this->createSlug($product);
            }
        });
        dd(Slug::query()->whereReferenceType(Product::class)->count());
    }

    public function createSlug($created_product)
    {
        try
        {
             Slug::create([
                'reference_type' => Product::class,
                'reference_id'   => $created_product->id,
                'key'            => Str::slug(Str::limit($created_product->name , 20 , '...')),
                'prefix'         => SlugHelperFacade::getPrefix(Product::class),
        ]);
        }catch(Throwable $ex){
            dd($ex);
        }

    }



    public function getProductsWithImageAndWithoutPrice()
    {
        // $products = Product::query()->where([['image' , '!=' , null] , ['weight'  , null] , ['price' , '!=' , 0]])
        //             ->orWhere([['image' , '!=' , null] , ['weight'  , 0] , ['price' , '!=' , 0]])
        //             ->update(['status' => BaseStatusEnum::PUBLISHED]);
        $products = Product::query()
                            ->where('image' , '!=' , null)
                            ->where('price' , 0)
                            ->orWhere('price' , null)
                            ->pluck('ean_code');

        dd($products);
    }

    public function getProductsWithoutDiscAttr()
    {
        $products = Product::query()->where('weight' , null)->orWhere('weight' , 0)->count();
        dd($products);
        // $no_img = Product::query()->whereStatus(BaseStatusEnum::PUBLISHED)->where('image' , null)->count();
        // $no_price = Product::query()->whereStatus(BaseStatusEnum::PUBLISHED)->where('price' , null)->orwhere('price' , 0)->count();
        // $no_weight = Product::query()->whereStatus(BaseStatusEnum::PUBLISHED)->where('weight' , null)->orwhere('weight' , 0)->count();
        // dd("no_img:".$no_img , "no_price:". $no_price , "no_weight:".$no_weight);
    }



    public function updateProductsWithoutPrice()
    {
        $products = Product::query()->wherePrice(0)->orWhere('price' , null)->count();
        dd($products);
    }



    // public function updatePublishedProductsTranslations()
    // {
    //     Product::query()
    //                 ->whereStatus(BaseStatusEnum::PUBLISHED)
    //                 ->whereHas('translations')->chunk(200 , function($products){
    //                     foreach($products as $product)
    //                     {
    //                         $this->updateProductTrans($);
    //                     }
    //                 });
    //     dd("Done");
    // }

        public function updateProductsTranslations()
        {
            $products = Product::query()->whereHas('variations')->pluck('ean_code');
            dd($products);
            @ini_set('max_execution_time', -1);
            @ini_set('memory_limit', -1);
        Product::query()->whereStatus(BaseStatusEnum::PUBLISHED)->chunk(200 , function($products){
            $languages = $this->getLanguages();
            foreach($products as $product)
            {
                sleep(5);
                foreach($languages as $lang)
                {
                    try{
                    $dist_lang = str_split($lang , 2)[0];
                    $tr = new GoogleTranslate($dist_lang);
                    if(($target = ProductTranslation::query()->where(['lang_code' => $lang , 'ec_products_id' => $product->id])->first() ) != null)
                    {
                        $target->update([
                        'name' =>  $tr->translate($product->name),
                        'description' => $tr->translate($product->description ?? $product->name),
                        'content' => $tr->translate($product->content),
                        'ec_products_id' => $product->id ,
                        'lang_code' => $lang,
                        ]);
                    }else{
                        ProductTranslation::create([
                            'name' =>  $tr->translate($product->name),
                            'description' => $tr->translate($product->description ?? $product->name),
                            'content' => $tr->translate($product->content),
                            'ec_products_id' => $product->id ,
                            'lang_code' => $lang,
                        ]);
                    }

                }catch(Throwable $e)
                {
                    dd($e);
                }

                }
            }
        });
        dd('Done');
        }


        public function getLanguages()
        {
            return ['ar' , 'nl_NL' , 'en_US'];
        }


}
