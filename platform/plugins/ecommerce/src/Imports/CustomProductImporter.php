<?php
namespace Botble\Ecommerce\Imports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\BrandTranslation;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductCategoryTranslation;
use Botble\Ecommerce\Models\ProductTranslation;
use Botble\Slug\Facades\SlugHelperFacade;
use Botble\Slug\Models\Slug;
use Botble\Slug\SlugHelper;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
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

        $product_rows = $products->slice(1);
        foreach($product_rows as $product)
        {
            $product = $this->trimProductData($product);
                try {
                    if ($product[2] != null && strlen(trim($product[2])) == 13) {
                        $this->createProduct($product);
                    }
                } catch (Throwable $e) {
                    info($e);
                }
        }
        return true;

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
     * create Product in DB
     */
    public function createProduct($product)
    {
        $ean = $product[2];
        $duplicated_product = Product::query()->whereEanCode($ean)->first();
        if($duplicated_product)
        {
            if($duplicated_product->order != 3) //if abood wasn't updated it i will update it
            {
                $to_en = new GoogleTranslate('en');
                //3 => category
                $created_product = $duplicated_product->update([
                    'name'      =>  $to_en->translate(@$product[0] ?? ""),
                    'note'      => $product[1],
                    'description' => $to_en->translate(@$product[5] ?? ""),
                    'image'     => @$product[6],
                    'images'    =>  $this->getProductImages(@$product[137]),
                    'brand_id'  =>  @$product[58] != null ? $this->getProductBrand(@$product[58]) : null,
                    'weight'    => $this->getProductWeight(@$product[206] , @$product[207]),
                    'wide'      => $this->getProductHeightAndWide(@$product[67] , @$product[68]),
                    'length'      => $this->getProductHeightAndWide(@$product[73] , @$product[74]),
                    'status' => BaseStatusEnum::PENDING,
                ]);
                $this->updateProductTranslations($created_product);
                $this->createSlug($created_product);
                $product_category_name = $to_en->translate(@$product[3]);
                $product_category_id = $this->getProductCategory($product_category_name);
                $created_product->categories()->sync($product_category_id);
                if((int)$created_product->price != 0 && $created_product->quantity != 0)
                {
                    $created_product->update(['status' => BaseStatusEnum::PUBLISHED]);
                }
            }
        }else{
            $to_en = new GoogleTranslate('en');

            //3 => category
            $created_product = Product::create([
                'name'      =>  $to_en->translate(@$product[0] ?? ""),
                'note'      => $product[1],
                'description' => $to_en->translate(@$product[5] ?? ""),
                'ean_code'  => $product[2],
                'sku'       => $this->generateBorvatCode(),
                'image'     => @$product[6],
                'images'    =>  $this->getProductImages(@$product[137]),
                'brand_id'  =>  @$product[58] != null ? $this->getProductBrand(@$product[58]) : null,
                'weight'    => $this->getProductWeight(@$product[206] , @$product[207]),
                'wide'      => $this->getProductHeightAndWide(@$product[67] , @$product[68]),
                'length'      => $this->getProductHeightAndWide(@$product[73] , @$product[74]),
                'status'    => BaseStatusEnum::PENDING,
            ]);
            $this->updateProductTranslations($created_product);
            $this->createSlug($created_product);
            $product_category_name = $to_en->translate(@$product[3]);
            $product_category_id = $this->getProductCategory($product_category_name);
            $created_product->categories()->sync($product_category_id);
        }
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

        if($images)
        {
            $exploded_images = explode(";" , $images);
            foreach ($exploded_images as $key => $tmp_image) {
                $product_images[$key] = str_replace(RvMedia::getUploadURL() . '/', '', trim($tmp_image));
            }
            return json_encode($product_images);
        }
        return null;
    }


    public function getProductWeight($weight  , $unit)
    {
        if($unit == 'kg')
        {
            $weight *= 1000; //to gm
        }
        return $weight;
    }

    public function getProductHeightAndWide($wide_height  , $unit)
    {
        if($unit == 'mmm')
        {
            $wide_height /= 100; //to cm
            return $wide_height;
        }elseif($unit == 'cm')
        {
            return $wide_height;
        }
    }

    public function getProductCategory($category_name)
    {
        $category = ProductCategory::query()->whereName($category_name)->first();
        if(!$category)
        {
            $category = ProductCategory::query()->create([
                'name' => $category_name
            ]);
            $this->createCategorySlug($category);
        }
        $languages = $this->getLanguages();
        foreach($languages as $lang)
        {
            $dist_lang = str_split($lang , 2)[0];
            $tr = new GoogleTranslate($dist_lang);
            ProductCategoryTranslation::firstOrCreate(['ec_product_categories_id' => $category->id  , 'lang_code' => $lang] ,
            [
                'name' =>  $tr->translate($category->name),
            ]);
        }
        return [$category->id];
    }


    public function createCategorySlug($category)
    {
            Slug::create([
                'reference_type' => ProductCategory::class,
                'reference_id'   => $category->id,
                'key'            =>  Str::slug(Str::limit(time().$category->name , 20 , '...')),
                'prefix'         => SlugHelperFacade::getPrefix(ProductCategory::class),
        ]);
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
            foreach($languages as $lang)
            {
                $dist_lang = str_split($lang , 2)[0];
                $tr = new GoogleTranslate($dist_lang);
                BrandTranslation::firstOrCreate(['ec_brands_id' => $brand->id  , 'lang_code' => $lang] ,
                [
                    'name' =>  $tr->translate($brand->name),
                ]);
            }
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

    public function updateProductTranslations(Product $product)
    {
        $languages = getLanguages();
        foreach($languages as $lang)
        {
            try{
                $dist_lang = str_split($lang , 2)[0];
                $tr = new GoogleTranslate((string)$dist_lang);
                $tr->setTarget((string)$dist_lang);
                if(ProductTranslation::query()->where([['lang_code' , $lang] , ['ec_products_id' , $product->id]])->first() != null)
                {
                    ProductTranslation::query()->where([['lang_code', $lang], ['ec_products_id', $product->id]])->update([
                    'name' =>  $tr->translate($product->name),
                    'description' => ($tr->translate(($product->description ?? ""))),
                    'content' => ($tr->translate(($product->content ?? ""))),
                    'ec_products_id' => $product->id ,
                    'lang_code' => $lang,
                    ]);
                }else{
                    ProductTranslation::create([
                        'name' =>  ($tr->translate($product->name)),
                        'description' => $tr->translate(($product->description ?? "") ),
                        'content' => $tr->translate(($product->content) ?? ""),
                        'ec_products_id' => $product->id ,
                        'lang_code' => $lang,
                    ]);
                }
        }catch(Throwable $e)
        {
            //Silent
                // dd($e);
        }

        }
    }





    /**
     * Generate Uique Borvat Code for each product
     */
    public function generateBorvatCode()
    {
        $borvat_code = 'BAC'.rand(1000 , 9000);
        if(Product::query()->where('sku' , $borvat_code)->exists())
        {
            return $this->generateBorvatCode();
        }
        return $borvat_code;
    }




    public function createSlug($created_product)
    {
        try
        {
            $s = Slug::create([
                'reference_type' => Product::class,
                'reference_id'   => $created_product->id,
                'key'            =>  Str::slug(Str::limit(time().$created_product->name , 20 , '...')),
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
