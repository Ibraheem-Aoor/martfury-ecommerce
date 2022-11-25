<?php
namespace Botble\Ecommerce\Imports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\BrandTranslation;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTranslation;
use Botble\Slug\Facades\SlugHelperFacade;
use Botble\Slug\Models\Slug;
use Botble\Slug\SlugHelper;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMapping;
use RvMedia;
use Throwable;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CustomProductImporter implements ToCollection
{


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $products)
    {
        try
        {
            $product_rows = $products->slice(1);
                foreach($product_rows as $product)
                {
                    try{
                        session()->put('product_rows' , $product_rows);
                        return true;
                        $product_array_values = $this->trimProductData($product);
                        $product = $this->updateProduct($product_array_values);
                        $product->save();
                    }catch(Throwable $ex){
                        dd($ex);
                    }
                }
                dd('Done Successfully');
            }catch(Throwable $ex)
            {
                dd($ex->getMessage());
            }

    }


    /**
     * Trim the porduct data from incoming collection object
     * @param  Collection $collection
     * @return array
     */
    public function trimProductData($collection) : array
    {
        return array_map('trim' , $collection->toArray());
    }


    /**
     * make a full product
     */

    public function updateProduct($product_array_values)
    {
        try
        {
        $product = Product::where('ean_code' , $product_array_values[1])->first();
        $product->update([
            'name' => \clean($product_array_values[2]),
            'price' => $this->getProductPrice($product_array_values[3]),
            'description' => \clean($product_array_values[4]),
            'content' => \clean($product_array_values[5]),
            'weight' => $product_array_values[6] != ""  ? $product_array_values[6] :0,
            'length' => $product_array_values[7] != "" ? $product_array_values[7] : 0,
            'wide' => $product_array_values[8] != ""  ? $product_array_values[8] :0,
            'height' => $product_array_values[9] != ""  ? $product_array_values[9] :0,
            'image' => $product_array_values[10] ,
            'images' => $product_array_values[11]  != null ?  $this->getProductImages($product_array_values[11]) : null ,
            'brand_id' => $product_array_values[12] != null ? $this->getProductBrand($product_array_values[12]) : null,
        ]);

        // $this->updateProductTranslations($product);
        if($product_array_values[0] == '*' || (int)$product->price == 0 || $product->weight == null || $product->weight == 0)
        {
            $product->status = BaseStatusEnum::PENDING;
            $product->save();
        }
        return $product;
    }catch(Throwable $e)
    {
        dd($e);
    }

    }

    public function getProductImages($images)
    {
        $images = explode('\n' , $images);
        foreach ($images as $key => $image) {
            $product_images[$key] = str_replace(RvMedia::getUploadURL() . '/', '', trim($image));
        }
        return json_encode($product_images);
    }



    /**
     * Product Price with sale of 20% from old price
     */
    public function getProductPrice($old_price)
    {
        try
        {
        return isset($old_price) && $old_price != "" ? ($old_price - (0.2 * $old_price) ) : 0;
        }catch(Throwable $e)
        {
            dd($old_price);
        }
    }



    public function getProductBrand($brand_name)
    {
        try{
            $brand = Brand::firstOrCreate(
                [
                    'name' => $brand_name,
                ],
                [
                    'name' =>  $brand_name,
                ]);
            $brand->save();
            $languages = $this->getLanguages();
            // foreach($languages as $lang)
            // {
            //     $dist_lang = str_split($lang , 2)[0];
            //     $tr = new GoogleTranslate($dist_lang);
            //     BrandTranslation::firstOrCreate(['ec_brands_id' => $brand->id  , 'lang_code' => $lang] ,
            //     [
            //         'name' =>  $tr->translate($brand->name),
            //     ]);
            // }
            return $brand->id;
        }catch(QueryException $e)
        {
            if($e->errorInfo[1] == 1062)
            {
                return Brand::query()->whereName($brand_name)->first()->id;
            }else{
                dd($e);
            }
        }catch(Throwable $e)
        {
            dd($e);
        }
    }

    public function updateProductTranslations($product)
    {
        $languages = $this->getLanguages();
        foreach($languages as $lang)
        {
            $dist_lang = str_split($lang , 2)[0];
            $tr = new GoogleTranslate($dist_lang);
            ProductTranslation::firstOrCreate(['ec_products_id' => $product->id , 'lang_code' => $lang] ,
            [
                'name' =>  $tr->translate($product->name),
                'description' => $tr->translate($product->description),
                'content' => $tr->translate($product->content),
            ]);
        }
    }





    public function createSlug($created_product)
    {
        try
        {
            $s = Slug::create([
                'reference_type' => Product::class,
                'reference_id'   => $created_product->id,
                'key'            => Str::slug($created_product->name),
                'prefix'         => SlugHelperFacade::getPrefix(Product::class),
        ]);
        }catch(Throwable $ex){
            dd($ex);
        }

    }


    public function getLanguages()
    {
        return ['ar' , 'nl_NL' , 'en_US'];
    }




}
