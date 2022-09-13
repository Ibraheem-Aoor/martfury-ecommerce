<?php

namespace Botble\Marketplace\Models;

use Botble\Base\Models\BaseModel;
use Exception;
use Hash;
use MarketplaceHelper;

class VendorInfo extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mp_vendor_info';

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'balance',
        'total_fee',
        'signature',
        'total_revenue',
        'bank_info',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'balance'       => 'decimal:2',
        'total_fee'     => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'bank_info'     => 'array',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (&$vendorInfo) {
            $model = new VendorInfo;
            $vendorInfo->balance = $vendorInfo->balance ?: 0;
            $vendorInfo->total_fee = $vendorInfo->total_fee ?: 0;
            $vendorInfo->total_revenue = $vendorInfo->total_revenue ?: 0;
            $vendorInfo->signature = Hash::make($model->getSignatureKey(false, $vendorInfo));

            return $vendorInfo;
        });

        static::updating(function (&$vendorInfo) {
            if ($vendorInfo->id) {
                $balanceOriginal = $vendorInfo->getOriginal('balance');
                $balance = $vendorInfo->balance;
                $totalFeeOriginal = $vendorInfo->getOriginal('total_fee');
                $totalFee = $vendorInfo->total_fee;
                $totalRevenueOriginal = $vendorInfo->getOriginal('total_revenue');
                $totalRevenue = $vendorInfo->total_revenue;
                if ($balanceOriginal != $balance || $totalFeeOriginal != $totalFee || $totalRevenueOriginal != $totalRevenue) {
                    if ($vendorInfo->isCheckSignature() && !$vendorInfo->checkSignature()) {
                        throw new Exception(__('Invalid signature of vendor info'));
                    }
                    $vendorInfo->signature = Hash::make($vendorInfo->getSignatureKey(true));
                }
            }

            return $vendorInfo;
        });
    }

    /**
     * @return bool
     */
    public function isCheckSignature()
    {
        return MarketplaceHelper::getSetting('check_valid_signature',
            config('plugins.marketplace.general.check_signature_vendor', true));
    }

    /**
     * @return string
     */
    public function checkSignature()
    {
        return Hash::check($this->getSignatureKey(), $this->signature);
    }

    /**
     * @return string
     */
    public function getSignatureKey($isNew = false, $item = null)
    {
        if (!$item) {
            $item = $this;
        }

        $balance = $isNew ? $item->balance : ($item->getOriginal('balance') ?: $item->balance);
        $totalFee = $isNew ? $item->total_fee : ($item->getOriginal('total_fee') ?: $item->total_fee);
        $totalRevenue = $isNew ? $item->total_revenue : ($item->getOriginal('total_revenue') ?: $item->total_revenue);

        return "$balance-$totalFee-$totalRevenue-{$item->customer_id}";
    }
}
