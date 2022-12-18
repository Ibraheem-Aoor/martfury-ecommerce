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

    protected $paymentCurrency;
    public function __construct()
    {
        $this->setConfigs();
    }


    /**
     * Set The PayNL Conifgurations
     */
    protected function setConfigs()
    {
        Config::setTokenCode("AT-0080-9493");
        Config::setApiToken('74f7899f27950f48adc53b2d8fca1183f7733e2b');
        Config::setServiceId('SL-7712-3492');
        $this->paymentCurrency = config('plugins.payment.payment.currency');
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
            info($e);
        }
    }







    public function makePayment(Request $request)
    {
        dd($request);
        $amount = round((float) $request->input('amount'),  2);
        $bank = $request->input('bank');
        $result = Transaction::start(array(
            # Required
                'amount' => $amount,
                'returnUrl' => route('public.payment.paypal.status' , 1),

            # Optional
                'currency' => 'EUR',
                'paymentMethod' => $request->input('payment_method'),
                'bank' => $bank,
                'description' => '',
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
        return $redirect;
    }



    public function getPaymentStatus(Request $request)
    {
        dd($request);
        $transactionId = $request->order_id;

        $transaction = Transaction::status($transactionId);

        # Manual transfer transactions are always pending when the user is returned
        if( $transaction->isPaid() || $transaction->isPending()) {
        # Redirect to thank you page
        } elseif($transaction->isCanceled()) {
        # Redirect back to checkout
        }
    }
}
