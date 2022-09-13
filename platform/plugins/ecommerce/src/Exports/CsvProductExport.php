<?php

namespace Botble\Ecommerce\Exports;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CsvProductExport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @var Collection
     */
    protected $results;

    /**
     * CsvProductExport constructor.
     */
    public function __construct()
    {
        $this->results = collect([]);

        app(ProductInterface::class)
            ->select(['*'])
            ->where('is_variation', 0)
            ->with([
                'categories',
                'slugable',
                'brand',
                'tax',
                'productLabels',
                'productCollections',
                'productAttributes.productAttributeSet',
                'variations',
                'variations.product',
                'variations.configurableProduct',
                'variations.productAttributes.productAttributeSet',
            ])
            ->chunk(400, function ($products) {
                $this->results = $this->results->concat(collect($this->productResults($products)));
            });
    }

    /**
     * @param Collection $products
     * @return array
     */
    public function productResults($products)
    {
        $results = [];
        foreach ($products as $product) {
            $productAttributes = [];
            if (!$product->variations->count()) {
                $productAttributes = $this->getProductAttributes($product);
            }

            $results[] = [
                'name'                             => $product->name,
                'description'                      => $product->description,
                'slug'                             => $product->slug,
                'sku'                              => $product->sku,
                // 'auto_generate_sku'                => '',
                'categories'                       => $product->categories->pluck('name')->implode(','),
                'status'                           => $product->status->getValue(),
                'is_featured'                      => $product->is_featured,
                'brand'                            => $product->brand->name,
                'product_collections'              => $product->productCollections->pluck('name')->implode(','),
                'labels'                           => $product->productLabels->pluck('name')->implode(','),
                'tax'                              => $product->tax->title,
                'images'                           => implode(',', $product->images),
                'price'                            => $product->price,
                'product_attributes'               => implode(',', $productAttributes),
                'import_type'                      => 'product',
                'is_variation_default'             => $product->is_variation_default,
                'stock_status'                     => $product->stock_status->getValue(),
                'with_storehouse_management'       => $product->with_storehouse_management,
                'quantity'                         => $product->quantity,
                'allow_checkout_when_out_of_stock' => $product->allow_checkout_when_out_of_stock,
                'sale_price'                       => $product->sale_price,
                'start_date_sale_price'            => $product->start_date,
                'end_date_sale_price'              => $product->end_date,
                'weight'                           => $product->weight,
                'length'                           => $product->length,
                'wide'                             => $product->wide,
                'height'                           => $product->height,
                'content'                          => $product->content,
            ];

            if ($product->variations->count()) {               
                foreach ($product->variations as $variation) {
                    $productAttributes = $this->getProductAttributes($variation);

                    $results[] = [
                        'name'                             => $variation->product->name,
                        'description'                      => '',
                        'slug'                             => '',
                        'sku'                              => $variation->product->sku,
                        // 'auto_generate_sku'                => '',
                        'categories'                       => '',
                        'status'                           => $variation->product->status->getValue(),
                        'is_featured'                      => '',
                        'brand'                            => '',
                        'product_collections'              => '',
                        'labels'                           => '',
                        'tax'                              => '',
                        'images'                           => implode(',', $variation->product->images),
                        'price'                            => $variation->product->price,
                        'product_attributes'               => implode(',', $productAttributes),
                        'import_type'                      => 'variation',
                        'is_variation_default'             => $variation->is_default,
                        'stock_status'                     => $variation->product->stock_status->getValue(),
                        'with_storehouse_management'       => $variation->product->with_storehouse_management,
                        'quantity'                         => $variation->product->quantity,
                        'allow_checkout_when_out_of_stock' => $variation->product->allow_checkout_when_out_of_stock,
                        'sale_price'                       => $variation->product->sale_price,
                        'start_date_sale_price'            => $variation->product->start_date,
                        'end_date_sale_price'              => $variation->product->end_date,
                        'weight'                           => $variation->product->weight,
                        'length'                           => $variation->product->length,
                        'wide'                             => $variation->product->wide,
                        'height'                           => $variation->product->height,
                        'content'                          => '',
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * @param Product|ProductVariation $product
     * @return array
     */
    public function getProductAttributes($product)
    {
        $productAttributes = [];
        foreach ($product->productAttributes as $productAttribute) {
            if ($productAttribute->productAttributeSet) {
                $productAttributes[] = $productAttribute->productAttributeSet->title . ':' . $productAttribute->title;
            }
        }
        return $productAttributes;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->results;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'name'                             => 'Product name',
            'description'                      => 'Description',
            'slug'                             => 'Slug',
            'sku'                              => 'SKU',
            // 'auto_generate_sku'                => 'Auto Generate SKU',
            'categories'                       => 'Categories',
            'status'                           => 'Status',
            'is_featured'                      => 'Is featured?',
            'brand'                            => 'Brand',
            'product_collections'              => 'Product collections',
            'labels'                           => 'Labels',
            'tax'                              => 'Tax',
            'images'                           => 'Images',
            'price'                            => 'Price',
            'product_attributes'               => 'Product attributes',
            'import_type'                      => 'Import type',
            'is_variation_default'             => 'Is variation default?',
            'stock_status'                     => 'Stock status',
            'with_storehouse_management'       => 'With storehouse management',
            'quantity'                         => 'Quantity',
            'allow_checkout_when_out_of_stock' => 'Allow checkout when out of stock',
            'sale_price'                       => 'Sale price',
            'start_date_sale_price'            => 'Start date sale price',
            'end_date_sale_price'              => 'End date sale price',
            'weight'                           => 'Weight',
            'length'                           => 'Length',
            'wide'                             => 'Wide',
            'height'                           => 'Height',
            'content'                          => 'Content',
        ];
    }
}
