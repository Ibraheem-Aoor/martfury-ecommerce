<?php

namespace Botble\Ecommerce\Models;

use Auth;
use Botble\ACL\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\Ecommerce\Enums\StockStatusEnum;
use Botble\Ecommerce\Facades\DiscountFacade;
use Botble\Ecommerce\Facades\FlashSaleFacade;
use Botble\Ecommerce\Services\Products\UpdateDefaultProductService;
use EcommerceHelper;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;

class Product extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ec_products';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'content',
        'deliverables',
        'image', // Featured image
        'images',
        'sku',
        'ean_code',
        'order',
        'quantity',
        'allow_checkout_when_out_of_stock',
        'with_storehouse_management',
        'is_featured',
        'brand_id',
        'is_variation',
        'sale_type',
        'price',
        'sale_price',
        'start_date',
        'end_date',
        'length',
        'wide',
        'height',
        'weight',
        'tax_id',
        'views',
        'stock_status',
        'deliverables',
        'created_by_id',
        'created_by_type',
        'store_id',
        'status',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'original_price',
        'front_sale_price',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status'       => BaseStatusEnum::class,
        'stock_status' => StockStatusEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Product $product) {
            $product->created_by_id = Auth::check() ? Auth::id() : 0;
            $product->created_by_type = User::class;
        });

        self::deleting(function (Product $product) {
            $variation = ProductVariation::where('product_id', $product->id)->first();
            if ($variation) {
                $variation->delete();
            }

            $productVariations = ProductVariation::where('configurable_product_id', $product->id)->get();

            foreach ($productVariations as $productVariation) {
                $productVariation->delete();
            }

            $product->categories()->detach();
            $product->productAttributeSets()->detach();
            $product->productAttributes()->detach();
            $product->productCollections()->detach();
            $product->discounts()->detach();
            $product->crossSales()->detach();
            $product->upSales()->detach();
            $product->groupedProduct()->detach();

            Review::where('product_id', $product->id)->delete();

            if (is_plugin_active('language-advanced')) {
                $product->translations()->delete();
            }
        });

        self::updated(function (Product $product) {
            if ($product->is_variation && $product->original_product->defaultVariation->product_id == $product->id) {
                app(UpdateDefaultProductService::class)->execute($product);
            }
        });
    }

    /**
     * @return BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'ec_product_category_product',
            'product_id',
            'category_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function productAttributeSets()
    {
        return $this->belongsToMany(
            ProductAttributeSet::class,
            'ec_product_with_attribute_set',
            'product_id',
            'attribute_set_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function productAttributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'ec_product_with_attribute', 'product_id', 'attribute_id');
    }

    /**
     * @return BelongsToMany
     */
    public function productCollections()
    {
        return $this->belongsToMany(
            ProductCollection::class,
            'ec_product_collection_products',
            'product_id',
            'product_collection_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'ec_discount_products', 'product_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function crossSales()
    {
        return $this->belongsToMany(Product::class, 'ec_product_cross_sale_relations', 'from_product_id',
            'to_product_id');
    }

    /**
     * @return BelongsToMany
     */
    public function upSales()
    {
        return $this->belongsToMany(Product::class, 'ec_product_up_sale_relations', 'from_product_id', 'to_product_id');
    }

    /**
     * @return BelongsToMany
     */
    public function groupedProduct()
    {
        return $this->belongsToMany(Product::class, 'ec_grouped_products', 'parent_product_id', 'product_id');
    }

    /**
     * @return BelongsToMany
     */
    public function productLabels()
    {
        return $this->belongsToMany(
            ProductLabel::class,
            'ec_product_label_products',
            'product_id',
            'product_label_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(
            ProductTag::class,
            'ec_product_tag_product',
            'product_id',
            'tag_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class)->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function products()
    {
        return $this
            ->belongsToMany(Product::class, 'ec_product_related_relations', 'from_product_id', 'to_product_id')
            ->where('is_variation', 0);
    }

    /**
     * @return HasMany
     */
    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'configurable_product_id');
    }

    /**
     * @return BelongsToMany
     */
    public function parentProduct()
    {
        return $this->belongsToMany(Product::class, 'ec_product_variations', 'product_id', 'configurable_product_id');
    }

    /**
     * @return HasMany
     */
    public function variationAttributeSwatchesForProductList()
    {
        return $this
            ->hasMany(ProductVariation::class, 'configurable_product_id')
            ->join('ec_product_variation_items', 'ec_product_variation_items.variation_id', '=',
                'ec_product_variations.id')
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=',
                'ec_product_attributes.attribute_set_id')
            ->where('ec_product_attribute_sets.status', BaseStatusEnum::PUBLISHED)
            ->where('ec_product_attribute_sets.is_use_in_product_listing', 1)
            ->select([
                'ec_product_attributes.*',
                'ec_product_variations.*',
                'ec_product_variation_items.*',
                'ec_product_attribute_sets.*',
                'ec_product_attributes.title as attribute_title',
            ]);
    }

    /**
     * @return HasOne
     */
    public function variationInfo()
    {
        return $this->hasOne(ProductVariation::class, 'product_id')->withDefault();
    }

    /**
     * @return HasOne
     */
    public function defaultVariation()
    {
        return $this
            ->hasOne(ProductVariation::class, 'configurable_product_id')
            ->where('ec_product_variations.is_default', 1)
            ->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function defaultProductAttributes()
    {
        return $this
            ->belongsToMany(ProductAttribute::class, 'ec_product_with_attribute', 'product_id', 'attribute_id')
            ->join(
                'ec_product_variation_items',
                'ec_product_variation_items.attribute_id',
                '=',
                'ec_product_with_attribute.attribute_id'
            )
            ->join('ec_product_variations', function ($join) {
                /**
                 * @var JoinClause $join
                 */
                return $join->on('ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
                    ->where('ec_product_variations.is_default', 1);
            })
            ->distinct();
    }

    /**
     * @return HasMany
     */
    public function groupedItems()
    {
        return $this->hasMany(GroupedProduct::class, 'parent_product_id');
    }

    /**
     * @param string $value
     * @return array
     */
    public function getImagesAttribute($value)
    {
        try {
            if ($value === '[null]') {
                return [];
            }

            $images = json_decode((string)$value, true);

            if (is_array($images)) {
                $images = array_filter($images);
            }

            return $images ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @param string $value
     * @return array
     */
    public function getOptionsAttribute($value)
    {
        try {
            return json_decode($value, true) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function getImageAttribute($value)
    {
        $firstImage = Arr::first($this->images) ?: null;

        if ($this->is_variation) {
            return $firstImage;
        }

        return $value ?: $firstImage;
    }

    /**
     * get sale price of product, if not exist return false
     * @return float
     */
    public function getFrontSalePriceAttribute()
    {
        if (!$this->is_variation) {
            $productCollections = $this->productCollections;
        } else {
            $productCollections = $this->original_product->productCollections;
        }

        $promotion = DiscountFacade::getFacadeRoot()
            ->promotionForProduct([$this->id], $productCollections->pluck('id')->all());

        if ($promotion) {
            $price = $this->price;
            switch ($promotion->type_option) {
                case 'same-price':
                    $price = $promotion->value;
                    break;
                case 'amount':
                    $price = $price - $promotion->value;
                    if ($price < 0) {
                        $price = 0;
                    }
                    break;
                case 'percentage':
                    $price = $price - ($price * $promotion->value / 100);
                    if ($price < 0) {
                        $price = 0;
                    }
                    break;
            }

            return $this->getComparePrice($price, $this->sale_price);
        }

        $flashSale = FlashSaleFacade::getFacadeRoot()->flashSaleForProduct($this);

        if ($flashSale && $flashSale->pivot->quantity > $flashSale->pivot->sold) {
            return $this->getComparePrice($flashSale->pivot->price, $this->sale_price);
        }

        return $this->getComparePrice($this->price, $this->sale_price);
    }

    /**
     * @param float $price
     * @param float $salePrice
     * @return mixed
     */
    protected function getComparePrice($price, $salePrice)
    {
        if ($salePrice && $price > $salePrice) {
            if ($this->sale_type == 0) {
                return $salePrice;
            }

            if ((!empty($this->start_date) && $this->start_date > now()) ||
                (!empty($this->end_date && $this->end_date < now()))) {
                return $price;
            }

            return $salePrice;
        }

        return $price;
    }

    /**
     * Get Original price of products
     * @return float
     */
    public function getOriginalPriceAttribute()
    {
        return $this->front_sale_price ?? $this->price ?? 0;
    }

    /**
     * @return BelongsToMany
     */
    public function attributesForProductList()
    {
        return $this
            ->belongsToMany(ProductAttribute::class, 'ec_product_with_attribute', 'product_id', 'attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=',
                'ec_product_attributes.attribute_set_id')
            ->where('ec_product_attribute_sets.status', BaseStatusEnum::PUBLISHED)
            ->where('ec_product_attribute_sets.is_use_in_product_listing', 1)
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.title as product_attribute_set_title',
                'ec_product_attribute_sets.slug as product_attribute_set_slug',
                'ec_product_attribute_sets.order as product_attribute_set_order',
                'ec_product_attribute_sets.display_layout as product_attribute_set_display_layout',
            ]);
    }

    /**
     * @return string|null
     */
    public function getStockStatusLabelAttribute()
    {
        if ($this->with_storehouse_management) {
            return $this->isOutOfStock() ? StockStatusEnum::OUT_OF_STOCK()->label() : StockStatusEnum::IN_STOCK()
                ->label();
        }

        return $this->stock_status->label();
    }

    /**
     * @return bool
     */
    public function isOutOfStock()
    {
        if (!$this->with_storehouse_management) {
            return $this->stock_status == StockStatusEnum::OUT_OF_STOCK;
        }

        return $this->quantity <= 0 && !$this->allow_checkout_when_out_of_stock;
    }

    /**
     * @return string|null
     */
    public function getStockStatusHtmlAttribute()
    {
        if ($this->with_storehouse_management) {
            return $this->isOutOfStock() ? StockStatusEnum::OUT_OF_STOCK()->toHtml() : StockStatusEnum::IN_STOCK()
                ->toHtml();
        }

        return $this->stock_status->toHtml();
    }

    /**
     * @param int $quantity
     * @return bool
     */
    public function canAddToCart(int $quantity)
    {
        return !$this->with_storehouse_management ||
            ($this->quantity - $quantity) >= 0 ||
            $this->allow_checkout_when_out_of_stock;
    }

    /**
     * @return BelongsToMany
     */
    public function promotions()
    {
        return $this
            ->belongsToMany(Discount::class, 'ec_discount_products', 'product_id')
            ->where('type', 'promotion')
            ->where('start_date', '<=', now())
            ->whereIn('target', ['specific-product', 'product-variant'])
            ->where(function ($query) {
                return $query
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where('product_quantity', 1);
    }

    /**
     * @return int|mixed|null
     */
    public function getOriginalProductAttribute()
    {
        if (!$this->is_variation) {
            return $this;
        }

        return $this->variationInfo->id ? $this->variationInfo->configurableProduct : $this;
    }

    /**
     * @return BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->original_product->belongsTo(Tax::class, 'tax_id')->withDefault();
    }

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('status', BaseStatusEnum::PUBLISHED);
    }

    /**
     * @return $this
     */
    public function latestFlashSales()
    {
        return $this->original_product
            ->belongsToMany(FlashSale::class, 'ec_flash_sale_products', 'product_id', 'flash_sale_id')
            ->withPivot(['price', 'quantity', 'sold'])
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->notExpired()
            ->latest();
    }

    /**
     * Get product sale price including taxes
     * @return float
     */
    public function getFrontSalePriceWithTaxesAttribute()
    {
        if (!EcommerceHelper::isDisplayProductIncludingTaxes()) {
            return $this->front_sale_price;
        }

        return $this->front_sale_price + $this->front_sale_price * ($this->tax->percentage / 100);
    }

    /**
     * Get product sale price including taxes
     * @return float
     */
    public function getPriceWithTaxesAttribute()
    {
        if (!EcommerceHelper::isDisplayProductIncludingTaxes()) {
            return $this->price;
        }

        return $this->price + $this->price * ($this->tax->percentage / 100);
    }

    /**
     * @return HasMany
     */
    public function variationProductAttributes()
    {
        return $this
            ->hasMany(ProductVariation::class, 'product_id')
            ->join('ec_product_variation_items', 'ec_product_variation_items.variation_id', '=',
                'ec_product_variations.id')
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=',
                'ec_product_attributes.attribute_set_id')
            ->distinct()
            ->select([
                'ec_product_variations.product_id',
                'ec_product_variations.configurable_product_id',
                'ec_product_attributes.*',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
            ])
            ->orderBy('order');
    }

    /**
     * @return string
     */
    public function getVariationAttributesAttribute()
    {
        if (!$this->variationProductAttributes->count()) {
            return '';
        }

        $attributes = $this->variationProductAttributes->pluck('title', 'attribute_set_title')->toArray();

        return '(' . mapped_implode(', ', $attributes, ': ') . ')';
    }

    /**
     * @return string
     */
    public function getPriceInTableAttribute()
    {
        $price = format_price($this->front_sale_price);

        if ($this->front_sale_price != $this->price) {
            $price .= ' <del class="text-danger">' . format_price($this->price) . '</del>';
        }

        return $price;
    }

    /**
     * @return MorphTo
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return array
     */
    public function getFaqItemsAttribute()
    {
        $this->loadMissing('metadata');
        $faqs = (array)$this->getMetaData('faq_schema_config', true);
        $faqs = array_filter($faqs);
        if (!empty($faqs)) {
            foreach ($faqs as $key => $item) {
                if (!$item[0]['value'] && !$item[1]['value']) {
                    Arr::forget($faqs, $key);
                }
            }
        }

        return $faqs;
    }

    /**
     * @return array
     */
    public function getReviewImagesAttribute()
    {
        $images = $this->reviews()
            ->select(['images', 'id'])
            ->where([
                'status' => BaseStatusEnum::PUBLISHED,
            ])
            ->whereNotNull('images')
            ->latest()
            ->get();

        $imagesReduced = $images->reduce(function ($carry, $item) {
            return array_merge($carry, $item->images);
        }, []);

        return $imagesReduced;
    }
}
