<?php

namespace Botble\Payment\Services\Gateways;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Abstracts\PaynlPaymentAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Botble\Payment\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Paynl\Config;
use Paynl\Paymentmethods;
use Paynl\Helper;
use Paynl\Transaction;
use Throwable;


class  PaynlPaymentService
{

    public function __construct()
    {
        $this->setConfigs();
    }


    /**
     * Set The PayNL Conifgurations
     */
    public function setConfigs()
    {
        Config::setTokenCode("AT-0080-9493");
        Config::setApiToken('74f7899f27950f48adc53b2d8fca1183f7733e2b');
        Config::setServiceId('SL-7712-3492');
    }
    /**
     *
     * @return array $paymentMethods
     */
    public function getPaymentMethods()
    {
        try{
            if(!Cache::get('paynl_payment_methods'))
            {
                $paymentMethods = Paymentmethods::getList();
                Cache::put('paynl_payment_methods', $paymentMethods, Carbon::now()->addDay());
                return $paymentMethods;
            }
            return Cache::get('paynl_payment_methods');
        }catch(Throwable $e)
        {
            //silent
        }
    }


    public function makePayment(Request $request)
    {
        $result = Transaction::start(array(
            # Required
                'amount' => 10.00,
                'returnUrl' => $request->input('callback_url'),

            # Optional
                'currency' => 'EUR',
                'exchangeUrl' => Helper::getBaseUrl().'/exchange.php',
                'paymentMethod' => 10,
                'bank' => 1,
                'description' => 'demo betaling',
                'testmode' => 1,
                'extra1' => 'ext1',
                'extra2' => 'ext2',
                'extra3' => 'ext3',
                'products' => array(
                    array(
                        'id' => 1,
                        'name' => 'een product',
                        'price' => 5.00,
                        'tax' => 0.87,
                        'qty' => 1,
                    ),
                    array(
                        'id' => 2,
                        'name' => 'ander product',
                        'price' => 5.00,
                        'tax' => 0.87,
                        'qty' => 1,
                    )
                ),
                'language' => 'EN',
                'ipaddress' => '127.0.0.1',
                'invoiceDate' => Carbon::today()->toDateString(),
                'deliveryDate' => Carbon::today()->toDateString() , // in case of tickets for an event, use the event date here
                'enduser' => array(
                    'initials' => 'T',
                    'lastName' => 'Test',
                    'gender' => 'M',
                    'birthDate' => new DateTime('1990-01-10'),
                    'phoneNumber' => '0612345678',
                    'emailAddress' => 'test@test.nl',
                ),
                'address' => array(
                    'streetName' => 'Test',
                    'houseNumber' => '10',
                    'zipCode' => '1234AB',
                    'city' => 'Test',
                    'country' => 'NL',
                ),
                'invoiceAddress' => array(
                    'initials' => 'IT',
                    'lastName' => 'ITEST',
                    'streetName' => 'Istreet',
                    'houseNumber' => '70',
                    'zipCode' => '5678CD',
                    'city' => 'ITest',
                    'country' => 'NL',
                ),
            ));

        # Save this transactionid and link it to your order
        $transactionId = $result->getTransactionId();

        # Redirect the customer to this url to complete the payment
        $redirect = $result->getRedirectUrl();
        return redirect($redirect);
    }
}
