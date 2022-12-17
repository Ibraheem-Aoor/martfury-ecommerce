<?php

namespace Botble\Payment\Services\Gateways;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Abstracts\PaynlPaymentAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Botble\Payment\Enums\PaymentStatusEnum;
use Paynl\Config;
use Paynl\Paymentmethods;
class  PaynlPaymentService
{

    public function getPaymentMethods()
    {
        # Replace tokenCode apitoken and serviceId with your own.
        Config::setTokenCode("AT-0080-9493");
        Config::setApiToken('74f7899f27950f48adc53b2d8fca1183f7733e2b');
        Config::setServiceId('SL-7712-3492');
        $paymentMethods = Paymentmethods::getBanks();
        return $paymentMethods;
    }
}
