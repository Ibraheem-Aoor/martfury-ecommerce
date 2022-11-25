<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\CustomProductImporter;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Botble\Ecommerce\Imports\VendorProductImport;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\BrandTranslation;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTranslation;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use Throwable;
use Stichoza\GoogleTranslate\GoogleTranslate;
use RvMedia;

class BulkImportController extends BaseController
{
    /**
     * @var ProductImport
     */
    protected $productImport;

    /**
     * @var ProductImport
     */
    protected $validateProductImport;

    /**
     * BulkImportController constructor.
     * @param ProductImport $productImport
     */
    public function __construct(ProductImport $productImport, ValidateProductImport $validateProductImport)
    {
        $this->productImport = $productImport;
        $this->validateProductImport = $validateProductImport;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        page_title()->setTitle(trans('plugins/ecommerce::bulk-import.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/ecommerce/js/bulk-import.js']);

        return view('plugins/ecommerce::bulk-import.index');
    }

    /**
     * @param BulkImportRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postImport(BulkImportRequest $request, BaseHttpResponse $response)
    {
        @ini_set('max_execution_time', -1);
        @ini_set('memory_limit', -1);

        $file = $request->file('file');
        $importer = new CustomProductImporter();
        ob_clean();
        if(FacadesExcel::import($importer , $file)){
            $product_rows = session()->get('product_rows');
            foreach($product_rows as $product)
            {
                try{
                    session()->put('product_rows' , $product_rows);
                    $product_array_values = $this->trimProductData($product);
                    $product = $this->updateProduct($product_array_values);
                    $product->save();
                }catch(Throwable $ex){
                    dd($ex);
                }
            }
            dd('Done Successfully');
        }else{
            $message = trans('plugins/ecommerce::bulk-import.import_failed_description');
        }
        return $response->setMessage($message);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(Request $request)
    {
        $extension = $request->input('extension');
        $extension = $extension == 'csv' ? $extension : Excel::XLSX;

        return (new TemplateProductExport($extension))->download('template_products_import.' . $extension);
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
            'price' => $this->getProductBasePrice($product_array_values[3]),
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
        if($product->price != 0)
        {
            $product->sale_price = $this->getProductSalePrice($product->price);
            $product->save();
        }
        $this->updateProductTranslations($product);
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


    public function getProductBasePrice($base_price)
    {
        return isset($old_price) && $old_price != "" ? $base_price: 0;
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
    public function getProductSalePrice($old_price)
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


    public function getLanguages()
    {
        return ['ar' , 'nl_NL' , 'en_US'];
    }


}
