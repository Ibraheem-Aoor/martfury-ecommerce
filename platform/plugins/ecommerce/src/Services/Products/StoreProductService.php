<?php

namespace Botble\Ecommerce\Services\Products;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\ProductRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class StoreProductService
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * StoreProductService constructor.
     * @param ProductInterface $product
     */
    public function __construct(ProductInterface $product)
    {
        $this->productRepository = $product;
    }

    /**
     * @param Request $request
     * @param Product $product
     * @param bool $forceUpdateAll
     * @return Product|false|Model
     */
    public function execute(Request $request, Product $product, bool $forceUpdateAll = false)
    {
        $data = $request->input();

        $hasVariation = $product->variations()->count() > 0;

        if ($hasVariation && !$forceUpdateAll) {
            $data = $request->except([
                'sku',
                'quantity',
                'allow_checkout_when_out_of_stock',
                'with_storehouse_management',
                'stock_status',
                'sale_type',
                'price',
                'sale_price',
                'start_date',
                'end_date',
                'length',
                'wide',
                'height',
                'weight',
            ]);
        }

        $product->fill($data);

        $images = [];

        if ($request->input('images', [])) {
            $images = array_values(array_filter($request->input('images', [])));
        }

        $product->images = json_encode($images);

        if (!$hasVariation || $forceUpdateAll) {
            if ($product->sale_price > $product->price) {
                $product->sale_price = null;
            }

            if ($product->sale_type == 0) {
                $product->start_date = null;
                $product->end_date = null;
            }
        }

        $exists = $product->id;

        /**
         * @var Product $product
         */
        if (Auth::check()) {
            $product->status = $request->input('price') == 0 ? BaseStatusEnum::PENDING :  $request->input('status');
        }
        if(auth('customer'))
            $product->status = BaseStatusEnum::PENDING;
        $product = $this->productRepository->createOrUpdate($product);

        if (!$exists) {
            event(new CreatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));
        } else {
            event(new UpdatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));
        }

        if ($product) {
            $categories = $request->input('categories');
            try
            {

            if(isset($categories[1]) && @$categories[1] == null)
                unset($categories[1]);
            if(isset($categories[1]) && @$categories[2] == null)
                unset($categories[2]);
            $product->categories()->sync($categories);

            }catch(Throwable $e)
            {
                
            }


            $product->productCollections()->sync($request->input('product_collections', []));

            $product->productLabels()->sync($request->input('product_labels', []));

            if ($request->has('related_products')) {
                $product->products()->detach();
                $product->products()->attach(array_filter(explode(',', $request->input('related_products', ''))));
            }

            if ($request->has('cross_sale_products')) {
                $product->crossSales()->detach();
                $product->crossSales()->attach(array_filter(explode(',', $request->input('cross_sale_products', ''))));
            }

            if ($request->has('up_sale_products')) {
                $product->upSales()->detach();
                $product->upSales()->attach(array_filter(explode(',', $request->input('up_sale_products', ''))));
            }
        }

        return $product;
    }
}
