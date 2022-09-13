<?php

namespace Botble\Marketplace\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static WithdrawalStatusEnum PENDING()
 * @method static WithdrawalStatusEnum PROCESSING()
 * @method static WithdrawalStatusEnum COMPLETED()
 * @method static WithdrawalStatusEnum CANCELLED()
 * @method static WithdrawalStatusEnum REFUSED()
 */
class WithdrawalStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
    public const CANCELED = 'canceled';
    public const REFUSED = 'refused';

    /**
     * @var string
     */
    public static $langPath = 'plugins/marketplace::withdrawal.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::PENDING:
                return Html::tag('span', self::PENDING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::PROCESSING:
                return Html::tag('span', self::PROCESSING()->label(), ['class' => 'label-primary status-label'])
                    ->toHtml();
            case self::COMPLETED:
                return Html::tag('span', self::COMPLETED()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::CANCELED:
                return Html::tag('span', self::CANCELED()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::REFUSED:
                return Html::tag('span', self::REFUSED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
