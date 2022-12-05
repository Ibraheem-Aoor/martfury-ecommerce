<?php

namespace App\Console\Commands;

use App\Services\Api\ApiService;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTranslation;
use Illuminate\Console\Command;
use Mews\Purifier\Casts\CleanHtml;
use Throwable;
use Stichoza\GoogleTranslate\GoogleTranslate;


class FetchBolProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bol-products:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Bol Products to fill thier attributes in all languages without human interception';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $api;
    protected $token;
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * genertate and set the auth berear token.
     */
    protected function generateToken()
    {
        $credits = config('bol-api.basic_auth_credits');
        $auth = $this->api->authWithBasic('token?grant_type=client_credentials' , $credits);
        $this->token = @$auth['access_token'];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*

        $base_url = config('bol-api.base_url');
        $headers = config('bol-api.headers');
        $this->api = new ApiService($base_url , null , $headers);
        $this->generateToken();
        $this->api->setToken($this->token);
        $eans = Product::query()->whereNull('content')->pluck('ean_code' , 'id');
        foreach($eans as $id => $ean)
        {
            try{
                $endpoint = 'content/catalog-products/'.$ean;
                $response = $this->api->get($endpoint);
                if(@$response['status'] == 401)
                {
                    $this->generateToken();
                    $this->api->setToken($this->token);
                    return $this->handle();
                }else{
                    $product = $this->getProductDataArray($response);
                    $product['id'] = $id;
                    $this->fillProductData($product);
                }
            }catch(Throwable $e)
            {
                info($e);
            }
        }
        */
    }


    /**
     * Get the needed product data to fill in DB
     */
    public function getProductDataArray(array $response)
    {
        $product = [];
        foreach($response['attributes'] as $attribute)
        {
            $key = $attribute['id'];
            $value = $attribute['values'][0]['value'];
            if($key == 'Height' || $key == 'Width' || $key == 'Length' || $key == 'Weight')
            {
                $current_unit = explode('.' , $attribute['values'][0]['unitId'])[2];
                $intended_unit =  $key == 'Weight' ?  'GRM' : 'CM';
                $product[$key] = $this->convertUnit($value , $current_unit , $intended_unit);
            }elseif($key == 'Description')
            {
                $product['description'] =  \clean($value);
            }elseif($key == 'Title')
            {
                $product['name'] = $value;
            }elseif($key == 'Language Packaging')
            {
                $product['packaging_language'] =  $value;
            }elseif($key == 'Whats In The Box')
            {
                $product['content'] =  \clean($value);
            }
        }
        return $product;
    }


    public function convertUnit($number , $current_unit ,  $intended_unit = 'CM')
    {
        if($intended_unit == $current_unit)
        {
            return $number;
        }elseif($intended_unit == 'CM' && $current_unit == 'MMT')
        {
            return $number / 10;
        }elseif($intended_unit == 'GRM' && $current_unit =='KGM')
        {
            return $number * 1000;
        }
    }


    /**
     * Fill Product Data in all langs
     */
    protected function fillProductData(array $product)
    {
        $languages = ['ar' , 'nl_NL' , 'en_US'];
        foreach($languages as $lang)
        {
            $dist_lang = str_split($lang , 2)[0];
            $tr = new GoogleTranslate($dist_lang);
            ProductTranslation::firstOrCreate(['ec_products_id' => $product['id'] , 'lang_code' => $lang] ,
            [
                'name' =>  $tr->translate($product['name']),
                'description' =>  $tr->translate($product['description'] ?? ""),
                'content' =>  $tr->translate($product['content'] ?? ""),
            ]
        );
        }
    }

}
