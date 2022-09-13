<?php

namespace Botble\Marketplace\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\Ecommerce\Models\Currency;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Marketplace\Enums\RevenueTypeEnum;
use Html;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenue extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mp_customer_revenues';

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'order_id',
        'sub_amount',
        'fee',
        'amount',
        'current_balance',
        'currency',
        'description',
        'user_id',
        'type',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'type' => RevenueTypeEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function currencyRelation(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency', 'title')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * @return string
     */
    public function getDescriptionTooltipAttribute(): string
    {
        if (!$this->description) {
            return '';
        }

        return Html::tag('span', '<i class="fa fa-info-circle text-info"></i>', [
            'class'                  => 'ms-1',
            'data-bs-toggle'         => 'tooltip',
            'data-bs-original-title' => $this->description,
            'title'                  => $this->description,
        ]);
    }
}
