<?php

namespace Botble\Marketplace\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static RevenueTypeEnum ADD_AMOUNT()
 * @method static RevenueTypeEnum SUBTRACT_AMOUNT()
 */
class RevenueTypeEnum extends Enum
{
    public const ADD_AMOUNT = 'add-amount';
    public const SUBTRACT_AMOUNT = 'subtract-amount';

    /**
     * @var string
     */
    public static $langPath = 'plugins/marketplace::revenue.types';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::ADD_AMOUNT:
                return Html::tag('span', self::ADD_AMOUNT()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::SUBTRACT_AMOUNT:
                return Html::tag('span', self::SUBTRACT_AMOUNT()->label(), ['class' => 'label-primary status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
