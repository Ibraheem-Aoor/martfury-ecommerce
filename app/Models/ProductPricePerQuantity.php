<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPricePerQuantity extends Model
{
    use HasFactory;
    protected $guarded = [];



    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class , 'ec_products_id');
    }
}
