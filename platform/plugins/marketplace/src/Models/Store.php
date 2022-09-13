<?php

namespace Botble\Marketplace\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Avatar;
use Botble\Base\Traits\EnumCastable;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RvMedia;

class Store extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mp_stores';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'country',
        'state',
        'city',
        'customer_id',
        'logo',
        'description',
        'content',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Store $store) {
            Product::where('store_id', $store->id)->delete();
            Discount::where('store_id', $store->id)->delete();
            Order::where('store_id', $store->id)->update(['store_id' => null]);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withDefault();
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->where('is_finished', 1);
    }

    /**
     * @return string
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return RvMedia::getImageUrl($this->logo, 'thumb');
        }

        try {
            return (new Avatar)->create($this->name)->toBase64();
        } catch (Exception $exception) {
            return RvMedia::getDefaultImage();
        }
    }

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Product::class)
            ->join('ec_reviews', 'ec_products.id', '=', 'ec_reviews.product_id');
    }
}
