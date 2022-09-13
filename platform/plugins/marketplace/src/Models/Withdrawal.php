<?php

namespace Botble\Marketplace\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

class Withdrawal extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mp_customer_withdrawals';

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'fee',
        'amount',
        'current_balance',
        'currency',
        'description',
        'payment_channel',
        'user_id',
        'status',
        'images',
        'bank_info',
        'transaction_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status'    => WithdrawalStatusEnum::class,
        'images'    => 'array',
        'bank_info' => 'array',
    ];

    /**
     * The "booted" method of the model.
     * @return void
     */
    protected static function booted()
    {
        static::updating(function (&$withdrawal) {
            if ($withdrawal->id) {
                $statusOriginal = $withdrawal->getOriginal('status')->getValue();
                $status = $withdrawal->{$withdrawal->getTable() . '.status'} ?: $withdrawal->status->getValue();

                if (in_array($statusOriginal, [
                    WithdrawalStatusEnum::CANCELED,
                    WithdrawalStatusEnum::REFUSED,
                    WithdrawalStatusEnum::COMPLETED,
                ])) {
                    $withdrawal->status = $statusOriginal;
                    $withdrawal->{$withdrawal->getTable() . '.status'} = $statusOriginal;
                    return $withdrawal;
                }

                if (in_array($statusOriginal, [WithdrawalStatusEnum::PROCESSING, WithdrawalStatusEnum::PENDING]) &&
                    in_array($status, [WithdrawalStatusEnum::CANCELED, WithdrawalStatusEnum::REFUSED])) {
                    $vendor = $withdrawal->customer;
                    $vendorInfo = $vendor->vendorInfo;
                    $vendorInfo->balance += ($withdrawal->amount + $withdrawal->fee);
                    $vendorInfo->save();
                }
            }
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
     * @return bool
     */
    public function getVendorCanEditAttribute(): bool
    {
        return $this->status->getValue() === WithdrawalStatusEnum::PENDING;
    }

    /**
     * @return bool
     */
    public function canEditStatus(): bool
    {
        return in_array($this->status->getValue(), [
            WithdrawalStatusEnum::PENDING,
            WithdrawalStatusEnum::PROCESSING,
        ]);
    }

    /**
     * @return array
     */
    public function getNextStatuses()
    {
        switch ($this->status->getValue()) {
            case WithdrawalStatusEnum::PENDING:
                $labels = Arr::except(WithdrawalStatusEnum::labels(), WithdrawalStatusEnum::COMPLETED);
                break;
            case WithdrawalStatusEnum::PROCESSING:
                $labels = Arr::except(WithdrawalStatusEnum::labels(), WithdrawalStatusEnum::PENDING);
                break;
            default:
                $labels = [$this->status->getValue() => $this->status->label()];
                break;
        }

        return $labels;
    }

    /**
     * @return string
     */
    public function getStatusHelper()
    {
        $status = $this->status->getValue();
        $key = 'plugins/marketplace::withdrawal.forms.' . $status . '_status_helper';

        return Lang::has($key) ? trans($key) : null;
    }
}
