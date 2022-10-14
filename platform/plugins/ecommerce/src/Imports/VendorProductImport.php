<?php
namespace Botble\Ecommerce\Imports;


use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMapping;

class VendorProductImport implements ToCollection
{


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $products)
    {
        $keys = array_values($products[0]->toArray());
        $product_rows = $products->slice(1);
        foreach($product_rows as $product)
        {
            $product_array_values = $this->trimProductData($product);
            $new_product['name']  = $product_array_values[0];
            $new_product['content']  = $product_array_values[1];
            $new_product['categproies']  = explode(',' , $product_array_values[2]);
            $new_product['brands']  = explode(',' , $product_array_values[3]);
            $new_product['price']  = $product_array_values[4];
            $new_product['quantity']  = $product_array_values[5];
            $new_product['delivery_time']  = $product_array_values[6];
            $new_product['attr_weight'] = $product_array_values[7];
            $new_product['attr_height'] = $product_array_values[8];
            $new_product['attr_width'] = $product_array_values[9];
            $new_product['attr_length'] = $product_array_values[10];
            $new_product['product_country'] = $product_array_values[11];
            $new_product['weight'] = $product_array_values[12];
            $new_product['height'] = $product_array_values[13];
            $new_product['width'] = $product_array_values[14];
            $new_product['length'] = $product_array_values[15];
            $new_product['guarntee'] = $product_array_values[16];
            $new_product['featured_image_url'] = $product_array_values[17];
            $new_product['images'] = explode(',' , $product_array_values[18]);
            dd($new_product);
            /**
             * Inser The recored Into DB
             */
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
     * make a key => value array from the given array
     */
    public function makeAssoicativeArray($indexedArray , $keys)
    {
    }
}
