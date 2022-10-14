<?php
namespace Botble\Ecommerce\Imports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Slug\Facades\SlugHelperFacade;
use Botble\Slug\Models\Slug;
use Botble\Slug\SlugHelper;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMapping;
use RvMedia;
use Throwable;

class VendorProductImport implements ToCollection
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
                        DB::beginTransaction();
                        $product_array_values = $this->trimProductData($product);
                        $new_product = $this->getNewProduct($product_array_values);
                        $created_product = Product::create($new_product);
                        $created_product->categories()->sync($new_product['categories']);
                        $this->createSlug($created_product);
                        DB::commit();
                    }catch(Throwable $ex){
                        dd($ex);
                        DB::rollBack();
                    }
                }
                return true;
            }catch(Throwable $ex)
            {
                return false;
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

    public function getNewProduct($product_array_values)
    {
        $ean_code = $product_array_values[0];
        $new_product['ean_code']  = $this->filterEanCode($ean_code);
        $new_product['name']  = $product_array_values[1];
        $new_product['content']  = $product_array_values[2];
        $new_product['categories']  = explode(',' , $product_array_values[3]);
        $new_product['brand_id']  =     $product_array_values[4];
        $new_product['price']  = $product_array_values[6];
        $new_product['quantity']  = $product_array_values[6];
        $new_product['delivery_time']  = $product_array_values[7];
        $new_product['attr_weight'] = (double)$product_array_values[8];
        $new_product['attr_height'] = (double)$product_array_values[9];
        $new_product['attr_width'] = (double)$product_array_values[10];
        $new_product['attr_length'] = (double)$product_array_values[11];
        $new_product['product_country'] = $product_array_values[12];
        $new_product['weight'] = (double)$product_array_values[13];
        $new_product['height'] = (double)$product_array_values[14];
        $new_product['width'] = (double)$product_array_values[15];
        $new_product['length'] = (double)$product_array_values[16];
        $new_product['guarantee'] = $product_array_values[17];
        $new_product['packaging_language'] = $product_array_values[18];
        $new_product['product_meterial'] = $product_array_values[19];
        $new_product['peice_count'] = $product_array_values[20];
        $new_product['image'] = $product_array_values[21];
        $new_product['created_by_id'] = auth('customer')->id();
        $new_product['status'] = BaseStatusEnum::PENDING;
        $new_product['model'] = Product::class;
        $new_product['store_id'] =  auth('customer')->user()->store->id;
        $images  = explode(',' , $product_array_values[22]);
        foreach ($images as $key => $image) {
            $images[$key] = str_replace(RvMedia::getUploadURL() . '/', '', trim($image));
        }
        $new_product['images'] = json_encode($images);
        return $new_product;
    }

    /**
     * Validating the given Ean code
     */

    public function filterEanCode($ean_code)
    {
        if((string)$ean_code[0]  == '"')
        {
            $ean_code = (int)filter_var($ean_code, FILTER_SANITIZE_NUMBER_INT);
        }
        return strlen($ean_code)  == 13 ? $ean_code : new Exception();
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







}
