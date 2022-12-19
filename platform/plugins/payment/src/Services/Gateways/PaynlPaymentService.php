<?php

namespace Botble\Payment\Services\Gateways;

use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Abstracts\PaynlPaymentAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
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
    protected function setConfigs()
    {
        Config::setTokenCode("AT-0080-9493");
        Config::setApiToken('74f7899f27950f48adc53b2d8fca1183f7733e2b');
        Config::setServiceId('SL-7712-3492');
    }


    public function execute(Request $request)
    {
        try
        {
            return $this->makePayment($request);
        }catch(Throwable $ex)
        {
            info('PAY_NL_EXCEPTON: ' . $ex);
            return false;
        }
    }









    protected function makePayment(Request $request)
    {
        $amount = round((float) $request->input('amount'),  2);
        $bank = $request->input('bank');
        $customer_id = $request->input('customer_id') ;
        $customer_data = [];
        if($customer_id && $request->input('customer_type') == Customer::class)
        {
            $customer_data = $this->getCutomerData($customer_id);
        }
        $order_id = is_array( $request->input('order_id') ) ? $request->input('order_id')[0]  : $request->input('order_id');
        $order = Order::query()->findOrFail($order_id);
        $address_array = $request->input('address');
        $address = [
            'streetName' => @$address_array['address'],
            'houseNumber' => @$address_array['house_no'],
            'zipCode' => @$address_array['zip_code'],
            'city' =>  @$address_array['city'],
            'country' => @$address_array['country'],
        ];
        $products = $this->getOrderProducts($order);
        $currency = $request->input('currency', config('plugins.payment.payment.currency'));
        $currency = strtoupper($currency);
        $queryParams = [
            'type'     => $this->getPaymentMethodName($request->input('payment_method')),
            'amount'   => $amount,
            'currency' => $currency,
            'order_id' => $request->input('order_id'),
            'customer_id' => $customer_id,
            'customer_type' => $request->input('customer_type') ,
        ];
        $result = Transaction::start(array(
            # Required
                'amount' => $amount,
                'returnUrl' => $request->input('callback_url') . '?' . http_build_query($queryParams),

            # Optional
                'currency' => 'EUR',
                'paymentMethod' => $request->input('payment_method'),
                'bank' => $bank,
                'description' => 'Borvat Order '. get_order_code($order->id),
                'testmode' => 0,
                'products' => $products,
                'language' => 'EN',
                'ipaddress' => '127.0.0.1',
                'invoiceDate' => Carbon::today()->toDateString(),
                'deliveryDate' => Carbon::today()->toDateString() , // in case of tickets for an event, use the event date here
                'enduser' => $customer_data ,
                'address' => $address,
                'invoiceAddress' => $address,
            ));
        # Save this transactionid and link it to your order
        $transactionId = $result->getTransactionId();

        # Redirect the customer to this url to complete the payment
        $redirect = $result->getRedirectUrl();
        return $redirect;
    }


    /**
       * Get the Payer Custoemr Data For Billing
       * @param int customer_id
       * @return array
       */
    public function getCutomerData($customer_id)
    {
        $customer = Customer::query()->findOrFail($customer_id);
        $full_name = explode(' ', $customer->name);
        return  [
            'initials' => $full_name[0], //first_name
            'lastName' => count($full_name) >  1 ? implode(' ' , array_slice($full_name , 1)) : null,
            'phoneNumber' => $customer->phone,
            'emailAddress' => $customer->email,
        ];
    }


    /**
      * get products for paynl
      * @param Order::class $products
      * @return array $products
      */
    protected function getOrderProducts($order)
    {
        $products = [];
        $order_products = $order->products;
        foreach($order_products as $product)
        {
            $single_product = [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => $product->price,
                'tax' => $product->tax_amount,
                'qty' => $product->qty,
            ];
            array_push($products ,  $single_product);
        }
        return $products;
    }

    public function getPaymentStatus(Request $request)
    {
        $transactionId = $request->orderId;

        $transaction = Transaction::status($transactionId);

        # Manual transfer transactions are always pending when the user is returned
        if( $transaction->isPaid() || $transaction->isPending() ||  $transaction->isAuthorized())
            return ['status' => true, 'transactionId' => $transactionId , 'transaction' => $transaction];
        else
        {
            $orderIds = (array)$request->input('order_id', []);
            $orders = Order::query()->whereIn('id', $orderIds)->get();
            $payment_data = [
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency'),
                'charge_id' => $transactionId,
                'customer_id' => $request->input('customer_id'),
                'customer_type' => $request->input('customer_type'),
                'payment_channel' => $request->input('type'),
                'status' =>  PaymentStatusEnum::FAILED,
            ];
            foreach ($orders as $order)
            {
                $payment_data['order_id'] = $order->id;
                Payment::query()->updateOrCreate(['order_id' => $order->id , 'customer_id' => $payment_data['customer_id'] , 'charge_id' =>  $payment_data['charge_id']] ,
                    $payment_data);
            }
            return ['status' => false  , 'transactionId' => $transactionId];
        }

    }


    public function finsihPayment($request)
    {
        $status = PaymentStatusEnum::COMPLETED;

        $chargeId = $request->orderId;

        $orderIds = (array)$request->input('order_id', []);

        $transactionId = $request->orderId;

        $transaction = Transaction::status($transactionId);

        if(!$transaction->isPaid() && !$transaction->isCanceled())
        {
            $status = PaymentStatusEnum::PENDING;
        }elseif($transaction->isCanceled())
        {
            $status = PaymentStatusEnum::FAILED;

        }else{
            $status = PaymentStatusEnum::COMPLETED;

        }


        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount'          => $request->input('amount'),
            'currency'        => $request->input('currency'),
            'charge_id'       => $chargeId,
            'order_id'        => $orderIds,
            'customer_id'     => $request->input('customer_id'),
            'customer_type'   => $request->input('customer_type'),
            'payment_channel' => $request->input('type'),
            'status'          => $status,
        ]);

        session()->forget('paypal_payment_id');

        return $chargeId;
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


    /**
     * @return string
     */
    protected function getPaymentMethodName($method_id)
    {
        $methods = $this->getPaymentMethods();
        foreach($methods as $method)
        {
            if($method['id'] == $method_id)
            {
                return @$method['brand']['name'];
            }
        }
    }
}
