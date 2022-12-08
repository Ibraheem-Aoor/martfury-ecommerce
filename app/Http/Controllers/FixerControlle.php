<?php

namespace App\Http\Controllers;

use Botble\Blog\Models\Category;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductTranslation;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class FixerControlle extends Controller
{
    public function getProductsWithoutTrans()
    {
    dd(Product::query()->find(8711526)->first());
        $products = Product::query()->whereDoesntHave('translations')->get();
        foreach ($products as $product)
            $this->updateProductTranslations($product);
        dd('All Products Have Translations Now');
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
                dd($e);
        }

        }
    }


    public function featureAllProducts()
    {
      @ini_set('max_execution_time', -1);
      @ini_set('memory_limit', -1);
      Product::query()->inRandomOrder()->take(400)->update(['is_featured' => 1]);
      ProductCategory::query()->where('parent_id' , 0) ->update(['is_featured' => 1]);
      $products = Product::query()->where('is_featured', 1)->count();
      $cats = ProductCategory::query()->where('is_featured', 1)->count();
      dd($products, $cats);
    }


}
