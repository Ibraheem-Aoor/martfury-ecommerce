<?php

namespace Botble\Marketplace\Providers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Models\Order as OrderModel;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Botble\Marketplace\Repositories\Interfaces\VendorInfoInterface;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Services\Gateways\PayPalPaymentService;
use Botble\Payment\Services\Gateways\StripePaymentService;
use Cart;
use EmailHandler;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;
use Illuminate\View\Factory;
use Log;
use MarketplaceHelper;
use OrderHelper;
use Throwable;

class OrderSupportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->booted(function () {
            add_filter(HANDLE_PROCESS_ORDER_DATA_ECOMMERCE, [$this, 'handleProcessOrder'], 100, 4);
            add_filter(HANDLE_PROCESS_POST_CHECKOUT_ORDER_DATA_ECOMMERCE, [$this, 'processPostCheckoutOrder'], 100, 5);
            add_filter(PROCESS_GET_CHECKOUT_SUCCESS_IN_ORDER, [$this, 'processGetCheckoutSuccess'], 100, 2);
            add_filter(PROCESS_GET_PAYMENT_STATUS_ORDER, [$this, 'processGetPaymentStatus'], 100, 2);
            add_filter(SEND_MAIL_AFTER_PROCESS_ORDER_MULTI_DATA, [$this, 'sendMailAfterProcessOrder'], 100);
            add_filter(PROCESS_CHECKOUT_ORDER_DATA_ECOMMERCE, [$this, 'processShippingDiscountOrderData'], 100, 4);
            add_filter(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, [$this, 'renderProductsInCheckoutPage'], 100);
            add_filter(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, [$this, 'processApplyCouponCode'], 100, 2);
            add_filter(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, [$this, 'processRemoveCouponCode'], 100, 2);
            add_filter(PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE, [$this, 'processPostSaveInformation'], 100, 3);
            add_filter(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, [$this, 'processGetCheckoutRecover'], 100, 2);
            add_filter(PROCESS_CHECOUT_RULES_REQUEST_ECOMMERCE, [$this, 'processCheckoutRulesRequest'], 100);
            add_filter(PROCESS_CHECOUT_MESSAGES_REQUEST_ECOMMERCE, [$this, 'processCheckoutMessagesRequest'], 100);
            add_action(ACTION_AFTER_ORDER_STATUS_COMPELETED_ECOMMERCE, [$this, 'afterOrderStatusCompleted'], 12);
        });
    }

    /**
     * @param Collection $products
     * @param string $token
     * @param array $sessionData
     * @param Request $request
     * @return array|array[]|\ArrayAccess[]
     */
    public function handleProcessOrder(Collection $products, string $token, array $sessionData, Request $request)
    {
        $groupedProducts = $this->cartGroupByStore($products);

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }
        $preOrders = collect([]);
        $mpSessionData = Arr::get($sessionData, 'marketplace', []);

        $orderIds = collect($mpSessionData ?: [])->pluck('created_order_id');
        if ($orderIds) {
            $preOrders = $this->app->make(OrderInterface::class)->allBy([['id', 'IN', $orderIds]], ['address']);
        }

        $foundOrderIds = [];

        $addressKeys = ['name', 'phone', 'email', 'country', 'state', 'city', 'addess', 'zip_code', 'address_id'];
        $addressData = Arr::only($sessionData, $addressKeys);

        foreach ($groupedProducts as $key => $productsByStore) {
            $sessionDataInStore = Arr::get($mpSessionData, $key, []);
            $order = $preOrders->firstWhere('store_id', $key);
            if ($order) {
                $foundOrderIds[] = $key;
            }
            $sessionDataInStore = array_merge($sessionDataInStore, $addressData);
            $mpSessionData[$key] = $this->handleOrderStore($productsByStore, $token, $sessionDataInStore, $request,
                $currentUserId, $order);
        }

        // Remove orders not exists pre checkout
        if ($preOrders) {
            foreach ($preOrders as $order) {
                if (!in_array($order->store_id, $foundOrderIds)) {
                    $order->delete();
                    if ($order->address && $order->address->id) {
                        $order->address->delete();
                    }
                }
            }
        }

        $sessionData = array_merge($sessionData, ['marketplace' => $mpSessionData]);
        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    /**
     * @param Collection $products
     * @return array|Collection
     */
    protected function cartGroupByStore(Collection $products)
    {
        if (!$products->count()) {
            return $products;
        }

        $products->loadMissing([
            'variationInfo',
            'variationInfo.configurableProduct',
            'variationInfo.configurableProduct.store',
        ]);

        $groupedProducts = collect([]);
        foreach ($products as $product) {
            $storeId = ($product->original_product && $product->original_product->store_id) ? $product->original_product->store_id : 0;
            if (!Arr::has($groupedProducts, $storeId)) {
                $groupedProducts[$storeId] = collect([
                    'store'    => $product->original_product->store,
                    'products' => collect([$product]),
                ]);
            } else {
                $groupedProducts[$storeId]['products'][] = $product;
            }
        }

        return $groupedProducts;
    }

    /**
     * @param Collection $products
     * @param string $token
     * @param array $sessionData
     * @param Request $request
     * @param int $currentUserId
     * @param OrderModel $order
     * @return array
     */
    public function handleOrderStore(
        Collection $products,
        string $token,
        array $sessionData,
        Request $request,
        $currentUserId,
        $order
    ) {
        $store = $products['store'];
        $cartItems = $products['products']->pluck('cartItem');

        $generalData = [
            'user_id'         => $currentUserId,
            'shipping_method' => $request->input('shipping_method.' . $store->id, ShippingMethodEnum::DEFAULT),
            'shipping_option' => $request->input('shipping_option.' . $store->id),
            'coupon_code'     => Arr::get($sessionData, 'applied_coupon_code'),
            'token'           => $token,
        ];

        [$sessionData, $order] = OrderHelper::processOrderInCheckout($sessionData, $request, $cartItems, $order,
            $generalData);

        Arr::set($sessionData, 'created_order_id', $order->id);
        $sessionData = OrderHelper::processAddressOrder($currentUserId, $sessionData, $request);

        $order->store_id = $store->id; // marketplace
        $order->save();

        $sessionData = OrderHelper::processOrderProductData($products, $sessionData);

        return $sessionData;
    }

    /**
     * @param Collection $products
     * @return string
     */
    public function renderProductsInCheckoutPage($products)
    {
        if ($products instanceof Collection) {
            $groupedProducts = $this->cartGroupByStore($products);
            $token = OrderHelper::getOrderSessionToken();
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

            return view('plugins/marketplace::orders.checkout.products',
                compact('groupedProducts', 'sessionCheckoutData'))->render();
        }

        return $products;
    }

    /**
     * @param Collection $products
     * @param Request $request
     * @param string $token
     * @param array $sessionCheckoutData
     * @param BaseHttpResponse $response
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function processPostCheckoutOrder($products, $request, $token, $sessionCheckoutData, $response)
    {
        $groupedProducts = $this->cartGroupByStore($products);

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }

        $orders = collect([]);

        $discounts = collect([]);
        $couponCode = session('applied_coupon_code');

        $preOrders = collect([]);

        $mpSessionData = Arr::get($sessionCheckoutData, 'marketplace', []);

        if ($couponCode) {
            $this->processApplyCouponCode([], $request);
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
            $couponCode = session()->get('applied_coupon_code');
        } else {
            foreach ($mpSessionData as &$storeCheckoutData) {
                Arr::set($storeCheckoutData, 'coupon_discount_amount', 0);
                Arr::set($storeCheckoutData, 'applied_coupon_code', null);
                Arr::set($storeCheckoutData, 'is_free_shipping', false);
            }
            $sessionCheckoutData = OrderHelper::setOrderSessionData($token, ['marketplace' => $mpSessionData]);
        }

        $mpSessionData = Arr::get($sessionCheckoutData, 'marketplace', []);

        $orderIds = collect($mpSessionData ?: [])->pluck('created_order_id');
        if ($orderIds) {
            $preOrders = $this->app->make(OrderInterface::class)->allBy([['id', 'IN', $orderIds]]);
        }
        $foundOrderIds = [];

        $promotionService = $this->app->make(HandleApplyPromotionsService::class);
        $shippingFeeService = $this->app->make(HandleShippingFeeService::class);
        $applyCouponService = $this->app->make(HandleApplyCouponService::class);

        foreach ($groupedProducts as $storeId => $productsInStore) {
            $sessionStoreData = Arr::get($mpSessionData, $storeId, []);

            $order = $preOrders->firstWhere('store_id', $storeId);
            if ($order) {
                $foundOrderIds[] = $storeId;
            }

            $orders[$storeId] = $this->handleCheckoutOrderByStore(
                $sessionCheckoutData,
                $productsInStore,
                $token,
                $sessionStoreData,
                $request,
                $currentUserId,
                $order,
                $storeId,
                $discounts,
                $promotionService,
                $shippingFeeService,
                $applyCouponService
            );
        }

        // Remove orders not exists pre checkout
        if ($preOrders) {
            foreach ($preOrders as $order) {
                if (!in_array($order->store_id, $foundOrderIds)) {
                    $order->delete();
                    if ($order->address && $order->address->id) {
                        $order->address->delete();
                    }
                }
            }
        }

        if ($couponCode && $discounts->count()) {
            $discount = $this->app->make(DiscountInterface::class)
                ->getModel()
                ->where('code', $couponCode)
                ->where('type', 'coupon')
                ->where('start_date', '<=', now())
                ->where(function ($query) {
                    /**
                     * @var Builder $query
                     */
                    return $query
                        ->whereNull('end_date')
                        ->orWhere('end_date', '>', now());
                })
                ->first();

            if (!empty($discount)) {
                $discount->total_used++;
                $this->app->make(DiscountInterface::class)->createOrUpdate($discount);
            }
        }

        $totalAmount = format_price($orders->pluck('amount')->sum(), null, true); // Calculator in here

        $paymentData = $this->processPaymentMethodPostCheckout($request, $totalAmount);

        if ($checkoutUrl = Arr::get($paymentData, 'checkoutUrl')) {
            return redirect($checkoutUrl);
        }

        if ($paymentData['error']) {
            return $response
                ->setError()
                ->setNextUrl(route('public.checkout.information', $token))
                ->setMessage($paymentData['message'] ?: __('Checkout error!'));
        }

        OrderHelper::processOrder($orders->pluck('id')->toArray(), $paymentData['charge_id']);

        return $response
            ->setNextUrl(route('public.checkout.success', $token))
            ->setMessage(__('Checkout successfully!'));
    }

    /**
     * @param array $sessionCheckoutData
     * @param array $products
     * @param string $token
     * @param array $sessionStoreData
     * @param Request $request
     * @param int $currentUserId
     * @param OrderModel $order
     * @param int $storeId
     * @param array|Collection $discounts
     * @param HandleApplyPromotionsService $promotionService
     * @param HandleShippingFeeService $shippingFeeService
     * @param HandleApplyCouponService $applyCouponService
     * @return mixed
     */
    public function handleCheckoutOrderByStore(
        $sessionCheckoutData,
        $products,
        $token,
        $sessionStoreData,
        $request,
        $currentUserId,
        $order,
        $storeId,
        &$discounts,
        $promotionService,
        $shippingFeeService,
        $applyCouponService
    ) {
        $shippingAmount = 0;

        $cartItems = $products['products']->pluck('cartItem');
        $rawTotal = Cart::instance('cart')->rawTotalByItems($cartItems);
        $countCart = Cart::instance('cart')->countByItems($cartItems);
        $couponCode = Arr::get($sessionStoreData, 'applied_coupon_code');

        $weight = 0;
        foreach ($products['products'] as $product) {
            if ($product->weight) {
                $weight += $product->weight * $product->cartItem->qty;
            }
        }

        $promotionDiscountAmount = $promotionService
            ->execute($token, compact('cartItems', 'rawTotal', 'countCart'), "marketplace.$storeId.");

        $couponDiscountAmount = 0;
        if ($couponCode) {
            $couponDiscountAmount = Arr::get($sessionStoreData, 'coupon_discount_amount', 0);
        }

        if ($request->has("shipping_method.$storeId") && !get_shipping_setting('free_ship',
                $request->input("shipping_method.$storeId"))) {

            $shippingData = [
                'address'     => Arr::get($sessionStoreData, 'address'),
                'country'     => Arr::get($sessionStoreData, 'country'),
                'state'       => Arr::get($sessionStoreData, 'state'),
                'city'        => Arr::get($sessionStoreData, 'city'),
                'weight'      => $weight ?: 0.1,
                'order_total' => $rawTotal - $promotionDiscountAmount - $couponDiscountAmount,
            ];

            $shippingMethod = $shippingFeeService
                ->execute($shippingData, $request->input("shipping_method.$storeId"),
                    $request->input("shipping_option.$storeId"));
            $shippingAmount = Arr::get(Arr::first($shippingMethod), 'price', 0);
        }

        if ($couponCode) {
            $discount = $applyCouponService->getCouponData($couponCode, $sessionStoreData);
            if ($discount) {
                if (!$discount->store_id || $discount->store_id == $storeId) {
                    $discounts->push($discount);
                    $shippingAmount = Arr::get($sessionStoreData, 'is_free_shipping') ? 0 : $shippingAmount;
                }
            }
        }

        if (($promotionDiscountAmount + $couponDiscountAmount - (float)$shippingAmount) > $rawTotal) {
            $orderAmount = 0;
        } else {
            $orderAmount = $rawTotal + (float)$shippingAmount - $promotionDiscountAmount - $couponDiscountAmount;
        }

        $data = array_merge($request->input(), [
            'amount'          => $orderAmount,
            'currency'        => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id'         => $currentUserId,
            'shipping_method' => $request->input("shipping_method.$storeId", ShippingMethodEnum::DEFAULT),
            'shipping_option' => $request->input("shipping_option.$storeId"),
            'shipping_amount' => (float)$shippingAmount,
            'tax_amount'      => Cart::instance('cart')->rawTaxByItems($cartItems),
            'sub_total'       => Cart::instance('cart')->rawSubTotalByItems($cartItems),
            'coupon_code'     => $couponCode,
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status'          => OrderStatusEnum::PENDING,
            'is_finished'     => true,
            'token'           => $token,
        ]);

        if ($order) {
            $order->fill($data);
            $order = $this->app->make(OrderInterface::class)->createOrUpdate($order);
        } else {
            $order = $this->app->make(OrderInterface::class)->createOrUpdate($data);
        }

        // Address Order in here
        $addressKeys = ['name', 'phone', 'email', 'country', 'state', 'city', 'addess', 'zip_code', 'address_id'];
        $addressData = Arr::only((array)$sessionCheckoutData, $addressKeys);
        $sessionStoreData = array_merge($sessionStoreData, $addressData);
        $sessionStoreData['created_order_id'] = $order->id;
        OrderHelper::processAddressOrder($currentUserId, $sessionStoreData, $request);

        $this->app->make(OrderHistoryInterface::class)->createOrUpdate([
            'action'      => 'create_order_from_payment_page',
            'description' => __('Order is created from checkout page'),
            'order_id'    => $order->id,
        ]);

        OrderHelper::processOrderProductData($products, $sessionStoreData);

        foreach ($cartItems as $cartItem) {
            $productByCartItem = $products['products']->firstWhere('id', $cartItem->id);

            $ids = [$productByCartItem->id];
            if ($productByCartItem->is_variation && $productByCartItem->original_product) {
                $ids[] = $productByCartItem->original_product->id;
            }

            $this->app->make(ProductInterface::class)
                ->getModel()
                ->whereIn('id', $ids)
                ->where('with_storehouse_management', 1)
                ->where('quantity', '>=', $cartItem->qty)
                ->decrement('quantity', $cartItem->qty);
        }

        $request->merge([
            'order_id' => array_merge($request->input('order_id', []), [$order->id]),
        ]);

        return $order;
    }

    /**
     * @param Request $request
     * @param int $totalAmount
     * @return array|mixed
     */
    public function processPaymentMethodPostCheckout($request, $totalAmount)
    {
        $paymentData = [
            'error'     => false,
            'message'   => false,
            'amount'    => round((float) $totalAmount, 2),
            'currency'  => $request->input('currency', strtoupper(cms_currency()->getDefaultCurrency()->title)),
            'type'      => $request->input('payment_method'),
            'charge_id' => null,
        ];

        $request->merge([
            'name'   => __('Pay for your order at :site_title', ['site_title' => theme_option('site_title')]),
            'amount' => $paymentData['amount'],
        ]);

        switch ($request->input('payment_method')) {
            case PaymentMethodEnum::STRIPE:
                $stripeService = $this->app->make(StripePaymentService::class);
                $result = $stripeService->execute($request);
                if ($stripeService->getErrorMessage()) {
                    $paymentData['error'] = true;
                    $paymentData['message'] = $stripeService->getErrorMessage();
                }

                $paymentData['charge_id'] = $result;

                break;

            case PaymentMethodEnum::PAYPAL:
                $payPalService = $this->app->make(PayPalPaymentService::class);

                $checkoutUrl = $payPalService->execute($request);
                if ($checkoutUrl) {
                    $paymentData['checkoutUrl'] = $checkoutUrl;
                } else {
                    $paymentData['error'] = true;
                    $paymentData['message'] = $payPalService->getErrorMessage();
                }

                break;
            case PaymentMethodEnum::COD:
                $paymentData['charge_id'] = $this->app->make(CodPaymentService::class)->execute($request);
                break;

            case PaymentMethodEnum::BANK_TRANSFER:
                $paymentData['charge_id'] = $this->app->make(BankTransferPaymentService::class)->execute($request);
                break;
            default:
                $paymentData = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $paymentData, $request);
                break;
        }

        return $paymentData;
    }

    /**
     * @param string $token
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function processGetCheckoutSuccess($token, $response)
    {
        if ($token !== session('tracked_start_checkout')) {
            return $response->setNextUrl(route('public.index'));
        }

        $orders = $this->app->make(OrderInterface::class)->allBy([
            'token'       => $token,
            'is_finished' => true,
        ], ['address', 'products']);

        if (!$orders->count()) {
            return $response->setNextUrl(route('public.index'));
        }

        OrderHelper::clearSessions($token);

        return view('plugins/marketplace::orders.thank-you', compact('orders'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     */
    public function processGetPaymentStatus($request, $response)
    {
        $token = session('tracked_start_checkout');

        if (!$token) {
            return $response->setNextUrl(route('public.index'));
        }

        $this->app->make(PayPalPaymentService::class)->afterMakePayment($request);

        return $response
            ->setNextUrl(route('public.checkout.success', $token))
            ->setMessage(__('Checkout successfully!'));
    }

    /**
     * @param Collection $orders
     * @return Collection
     */
    public function sendMailAfterProcessOrder($orders)
    {
        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('admin_new_order')) {
            $this->setEmailVariables($orders);
            $mailer->sendUsingTemplate('admin_new_order');
        }

        $this->sendOrderConfirmationEmail($orders, true);

        $this->sendMailToVendorAfterProcessingOrder($orders);

        return $orders;
    }

    /**
     * @param Collection $orders
     * @return \Botble\Base\Supports\EmailHandler
     * @throws Throwable
     */
    public function setEmailVariables($orders)
    {
        $theFirst = $orders->first();

        $customerAddress = $theFirst->full_address;

        return EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'store_address'    => get_ecommerce_setting('store_address'),
                'store_phone'      => get_ecommerce_setting('store_phone'),
                'order_id'         => '',
                'order_token'      => $theFirst->token,
                'customer_name'    => $theFirst->user->name ?: $theFirst->address->name,
                'customer_email'   => $theFirst->user->email ?: $theFirst->address->email,
                'customer_phone'   => $theFirst->user->phone ?: $theFirst->address->phone,
                'customer_address' => $customerAddress,
                'product_list'     => view('plugins/marketplace::emails.partials.order-detail', compact('orders'))
                    ->render(),
                'shipping_method'  => $theFirst->shipping_method_name,
                'payment_method'   => $theFirst->payment->payment_channel->label(),
            ]);
    }

    /**
     * @param OrderModel $order
     * @param bool $saveHistory
     * @return boolean
     * @throws Throwable
     */
    public function sendOrderConfirmationEmail($orders, $saveHistory = false)
    {
        try {
            $theFirst = $orders->first();
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('customer_new_order')) {
                $this->setEmailVariables($orders);

                EmailHandler::send(
                    $mailer->getTemplateContent('customer_new_order'),
                    $mailer->getTemplateSubject('customer_new_order'),
                    $theFirst->user->email ?: $theFirst->address->email
                );

                if ($saveHistory) {
                    foreach ($orders as $order) {
                        $this->app->make(OrderHistoryInterface::class)->createOrUpdate([
                            'action'      => 'send_order_confirmation_email',
                            'description' => trans('plugins/ecommerce::order.confirmation_email_was_sent_to_customer'),
                            'order_id'    => $order->id,
                        ]);
                    }
                }
            }

            return true;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        return false;
    }

    /**
     * @param Collection $orders
     * @return Collection
     */
    public function sendMailToVendorAfterProcessingOrder($orders)
    {
        $orders->loadMissing(['store']);
        $mailer = EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME);

        if ($mailer->templateEnabled('store_new_order')) {
            foreach ($orders as $order) {
                if ($order->store->email) {
                    $this->setEmailVendorVariables($order);
                    $mailer->sendUsingTemplate('store_new_order', $order->store->email);
                }
            }
        }

        return $orders;
    }

    /**
     * @param OrderModel $order
     * @return \Botble\Base\Supports\EmailHandler
     * @throws Throwable
     */
    public function setEmailVendorVariables(OrderModel $order)
    {
        return EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'customer_name'    => $order->user->name ?: $order->address->name,
                'customer_email'   => $order->user->email ?: $order->address->email,
                'customer_phone'   => $order->user->phone ?: $order->address->phone,
                'customer_address' => $order->full_address,
                'product_list'     => view('plugins/ecommerce::emails.partials.order-detail', compact('order'))
                    ->render(),
                'shipping_method'  => $order->shipping_method_name,
                'payment_method'   => $order->payment->payment_channel->label(),
                'store_name'       => $order->store->name,
            ]);
    }

    /**
     * @param Collection $products
     * @param string $token
     * @param array $sessionCheckoutData
     * @param Request $request
     * @return array
     */
    public function processShippingDiscountOrderData($products, $token, $sessionCheckoutData, $request)
    {
        $groupedProducts = $this->cartGroupByStore($products);

        $mpSessionCheckoutData = Arr::get($sessionCheckoutData, 'marketplace');

        $couponCode = session()->get('applied_coupon_code');
        if ($couponCode) {
            $this->processApplyCouponCode([], $request);
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
            $couponCode = session()->get('applied_coupon_code');
        } else {
            foreach ($mpSessionCheckoutData as &$storeCheckoutData) {
                Arr::set($storeCheckoutData, 'coupon_discount_amount', 0);
                Arr::set($storeCheckoutData, 'applied_coupon_code', null);
                Arr::set($storeCheckoutData, 'is_free_shipping', false);
            }
            $sessionCheckoutData = OrderHelper::setOrderSessionData($token, ['marketplace' => $mpSessionCheckoutData]);
        }

        $mpSessionCheckoutData = Arr::get($sessionCheckoutData, 'marketplace');
        $discounts = collect([]);

        $marketplaceData = collect([]);

        $promotionService = $this->app->make(HandleApplyPromotionsService::class);
        $shippingFeeService = $this->app->make(HandleShippingFeeService::class);
        $applyCouponService = $this->app->make(HandleApplyCouponService::class);

        $shipping = [];

        foreach ($groupedProducts as $storeId => $productsByStore) {
            $cartItems = $productsByStore['products']->pluck('cartItem');
            $vendorSessionData = Arr::get($mpSessionCheckoutData, $storeId);
            $weight = 0;
            foreach ($cartItems as $cartItem) {
                $productByCartItem = $productsByStore['products']->firstWhere('id', $cartItem->id);
                $weight += $productByCartItem->weight * $cartItem->qty;
            }

            $rawTotal = Cart::instance('cart')->rawTotalByItems($cartItems);
            $countCart = Cart::instance('cart')->countByItems($cartItems);

            $prefixPromotion = "marketplace.$storeId.";
            $promotionDiscountAmount = $promotionService->execute($token, compact('cartItems', 'rawTotal', 'countCart'),
                $prefixPromotion);

            $couponDiscountAmount = 0;
            if ($couponCode) {
                $couponDiscountAmount = Arr::get($vendorSessionData, 'coupon_discount_amount', 0);
            }

            $rawTotal = $rawTotal - $promotionDiscountAmount;
            $rawTotal = $rawTotal > 0 ? $rawTotal : 0;

            $shippingData = [
                'address'     => Arr::get($sessionCheckoutData, 'address'),
                'country'     => Arr::get($sessionCheckoutData, 'country'),
                'state'       => Arr::get($sessionCheckoutData, 'state'),
                'city'        => Arr::get($sessionCheckoutData, 'city'),
                'weight'      => $weight,
                'order_total' => $rawTotal,
            ];

            $shipping = $shippingFeeService->execute($shippingData);

            foreach ($shipping as $key => &$shipItem) {
                if (get_shipping_setting('free_ship', $key)) {
                    foreach ($shipItem as &$subShippingItem) {
                        Arr::set($subShippingItem, 'price', 0);
                    }
                }
            }

            $defaultShippingMethod = $request->input("shipping_method.$storeId",
                old("shipping_method.$storeId",
                    Arr::get($vendorSessionData, 'shipping_method', Arr::first(array_keys($shipping)))));

            $defaultShippingOption = null;
            if (!empty($shipping)) {
                $defaultShippingOption = Arr::first(array_keys(Arr::first($shipping)));

                if ($optionRequest = $request->input("shipping_option.$storeId",
                    old("shipping_option.$storeId"))) {
                    $defaultShippingOption = $optionRequest;
                } else {
                    $defaultShippingOption = Arr::get($vendorSessionData, 'shipping_option', $defaultShippingOption);
                }
            }

            $shippingAmount = Arr::get($shipping, "$defaultShippingMethod.$defaultShippingOption.price", 0);

            Arr::set($vendorSessionData, 'shipping_method', $defaultShippingMethod);
            Arr::set($vendorSessionData, 'shipping_option', $defaultShippingOption);
            Arr::set($vendorSessionData, 'shipping_amount', $shippingAmount);

            OrderHelper::setOrderSessionData($token, ['marketplace' => [$storeId => $vendorSessionData]]);

            if ($couponCode) {
                if (!$request->input('applied_coupon')) {
                    $discount = $applyCouponService->getCouponData($couponCode, $vendorSessionData);

                    if ($discount) {
                        if (!$discount->store_id || $discount->store_id == $storeId) {
                            $discounts->push($discount);
                            $shippingAmount = Arr::get($vendorSessionData, 'is_free_shipping') ? 0 : $shippingAmount;
                        }
                    }
                } else {
                    $shippingAmount = Arr::get($vendorSessionData, 'is_free_shipping') ? 0 : $shippingAmount;
                }
            }

            $marketplaceData[$storeId] = [
                'shipping'                  => $shipping,
                'default_shipping_method'   => $defaultShippingMethod,
                'default_shipping_option'   => $defaultShippingOption,
                'shipping_amount'           => $shippingAmount,
                'promotion_discount_amount' => $promotionDiscountAmount,
                'coupon_discount_amount'    => $couponDiscountAmount,
            ];
        }

        $shippingAmount = $marketplaceData->pluck('shipping_amount')->sum();
        $promotionDiscountAmount = $marketplaceData->pluck('promotion_discount_amount')->sum();
        $couponDiscountAmount = $marketplaceData->pluck('coupon_discount_amount')->sum();

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

        $mpSessionCheckoutData = Arr::get($sessionCheckoutData, 'marketplace');

        foreach ($mpSessionCheckoutData as $storeId => $mpSessionData) {
            Arr::set($mpSessionCheckoutData, $storeId,
                array_merge($mpSessionData, Arr::get($marketplaceData, $storeId, [])));
        }

        $sessionCheckoutData = OrderHelper::mergeOrderSessionData($token, ['marketplace' => $mpSessionCheckoutData]);

        return [
            $sessionCheckoutData,
            $shipping,
            $defaultShippingMethod,
            $defaultShippingOption,
            $shippingAmount,
            $promotionDiscountAmount,
            $couponDiscountAmount,
        ];
    }

    /**
     * @param array $result
     * @param Request $request
     * @return array
     */
    public function processApplyCouponCode($result, $request)
    {
        $products = Cart::instance('cart')->products();
        $groupedProducts = $this->cartGroupByStore($products);
        $token = OrderHelper::getOrderSessionToken();

        if (!$token) {
            $token = OrderHelper::getOrderSessionToken();
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        $sessionMarketplaceData = Arr::get($sessionCheckoutData, 'marketplace', []);
        $results = collect([]);
        $couponCode = $request->input('coupon_code');

        if (!$couponCode) {
            $couponCode = session('applied_coupon_code');
        }

        foreach ($groupedProducts as $storeId => $groupedProduct) {
            $cartItems = $groupedProduct['products']->pluck('cartItem');
            $rawTotal = Cart::instance('cart')->rawTotalByItems($cartItems);
            $countCart = Cart::instance('cart')->countByItems($cartItems);
            $sessionData = Arr::get($sessionMarketplaceData, $storeId, []);
            $prefix = "marketplace.$storeId.";
            $result = $this->app->make(HandleApplyCouponService::class)
                ->execute($couponCode, $sessionData, compact('cartItems', 'rawTotal', 'countCart'), $prefix);
            $results[$storeId] = $result;
        }

        $error = 0;
        $message = '';
        $successData = [
            'error' => true,
            'data'  => [],
        ];

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        $sessionMarketplaceData = Arr::get($sessionCheckoutData, 'marketplace', []);

        foreach ($results as $storeId => $result) {
            $sessionData = Arr::get($sessionMarketplaceData, $storeId, []);
            if (Arr::get($result, 'error')) {
                $error += 1;
                $message = Arr::get($result, 'message');

                Arr::set($sessionData, 'coupon_discount_amount', 0);
                Arr::set($sessionData, 'applied_coupon_code', null);
                Arr::set($sessionData, 'is_free_shipping', false);
            } else {
                $discount = Arr::get($result, 'data.discount');
                if ((!$discount->store_id || $discount->store_id == $storeId) &&
                    (Arr::get($result, 'data.is_free_shipping', false) || Arr::get($result, 'data.discount_amount'))) {
                    $successData = $result;
                    Arr::set($sessionData, 'applied_coupon_code', $couponCode);
                } else {
                    Arr::set($sessionData, 'coupon_discount_amount', 0);
                    Arr::set($sessionData, 'applied_coupon_code', null);
                    Arr::set($sessionData, 'is_free_shipping', false);
                    $message = __('Coupon code is not valid or does not apply to the products');
                    $error += 1;
                }
            }

            Arr::set($sessionMarketplaceData, $storeId, $sessionData);
        }

        // return if all are error
        if ($results->count() == $error) {
            session()->forget('applied_coupon_code');
            return compact('error', 'message');
        }

        $isFreeShipping = Arr::get($successData, 'data.is_free_shipping', false);
        $discountTypeOption = Arr::get($successData, 'data.discount_type_option');

        if (!$isFreeShipping && !in_array($discountTypeOption, ['percentage', 'same-price'])) {
            $validRawTotals = 0;

            foreach ($groupedProducts as $storeId => $groupedProduct) {
                $result = Arr::get($results, $storeId, []);
                if (!Arr::get($result, 'error') && Arr::get($result, 'data.discount_amount')) {
                    $discount = Arr::get($result, 'data.discount');
                    if ($discount && (!$discount->store_id || $discount->store_id == $storeId)) {
                        $cartItems = $groupedProduct['products']->pluck('cartItem');
                        $rawTotal = Cart::instance('cart')->rawTotalByItems($cartItems);
                        $validRawTotals += $rawTotal;
                    }
                }
            }
            $totalDiscountAmount = Arr::get($successData, 'data.discount_amount', 0);
            if ($validRawTotals) {
                foreach ($groupedProducts as $storeId => $groupedProduct) {
                    $result = Arr::get($results, $storeId, []);
                    $sessionData = Arr::get($sessionMarketplaceData, $storeId, []);
                    $discountAmount = 0;
                    if (Arr::get($result, 'error') || !Arr::get($result, 'data.discount_amount')) {
                        Arr::set($sessionData, 'coupon_discount_amount', $discountAmount);
                        Arr::set($sessionData, 'applied_coupon_code', null);
                    } else {
                        $discount = Arr::get($result, 'data.discount');
                        if ($discount && (!$discount->store_id || $discount->store_id == $storeId)) {
                            $cartItems = $groupedProduct['products']->pluck('cartItem');
                            $rawTotal = Cart::instance('cart')->rawTotalByItems($cartItems);
                            $discountAmount = round($totalDiscountAmount / $validRawTotals * $rawTotal, 2);
                            Arr::set($sessionData, 'applied_coupon_code', $couponCode);
                        } else {
                            Arr::set($sessionData, 'applied_coupon_code', null);
                        }
                        Arr::set($sessionData, 'coupon_discount_amount', $discountAmount);
                    }
                    Arr::set($sessionMarketplaceData, $storeId, $sessionData);
                }
            }
        }

        $couponDiscountAmount = collect($sessionMarketplaceData)->sum('coupon_discount_amount');

        OrderHelper::setOrderSessionData($token, [
            'marketplace'            => $sessionMarketplaceData,
            'coupon_discount_amount' => $couponDiscountAmount,
        ]);

        return $successData;
    }

    /**
     * @param Collection $products
     * @return array
     */
    public function processRemoveCouponCode($products)
    {
        $groupedProducts = $this->cartGroupByStore($products);

        $results = collect([]);

        foreach ($groupedProducts as $storeId => $groupedProduct) {
            $prefix = "marketplace.$storeId.";
            $result = $this->app->make(HandleRemoveCouponService::class)->execute($prefix, false);
            $results[$storeId] = $result;
        }

        session()->forget('applied_coupon_code');

        $error = 0;
        $message = '';
        $successData = [
            'error' => true,
            'data'  => [],
        ];

        foreach ($results as $result) {
            if (Arr::get($result, 'error')) {
                $error += 1;
                $message = Arr::get($result, 'message');
            } else {
                $successData = $result;
            }
        }

        // return if all are error
        if ($results->count() == $error) {
            return compact('error', 'message');
        }

        return $successData;
    }

    /**
     * @param array $sessionCheckoutData
     * @param Request $request
     * @param string $token
     * @return array|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function processPostSaveInformation($sessionCheckoutData, $request, $token)
    {
        if (session()->has('applied_coupon_code')) {
            $discounts = collect([]);
            $mpSessionData = Arr::get($sessionCheckoutData, 'marketplace', []);
            foreach ($mpSessionData as $storeId => $sessionStoreData) {
                $discount = $this->app->make(HandleApplyCouponService::class)
                    ->getCouponData(session('applied_coupon_code'), $sessionStoreData);
                if (!$discount) {
                    $discounts->push($discount);
                    $prefix = "marketplace.$storeId.";
                    $this->app->make(HandleRemoveCouponService::class)->execute($prefix, false);
                }
            }

            if (count($mpSessionData) == $discounts->count()) {
                session()->forget('applied_coupon_code');
            }

            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        }
        $mpSessionData = Arr::get($sessionCheckoutData, 'marketplace', []);

        $addressKeys = ['name', 'phone', 'email', 'country', 'state', 'city', 'address', 'zip_code', 'address_id'];
        $addressData = Arr::only((array)$request->input('address', []), $addressKeys);

        foreach ($mpSessionData as $storeId => $sessionStoreData) {
            Arr::set($mpSessionData, $storeId, array_merge($sessionStoreData, $addressData));
        }

        Arr::set($sessionCheckoutData, 'marketplace', $mpSessionData);

        return $sessionCheckoutData;
    }

    /**
     * @param string $token
     * @param Request $request
     * @return Application|Factory|View
     */
    public function processGetCheckoutRecover($token, $request)
    {
        $orders = $this->app->make(OrderInterface::class)
            ->allBy([
                'token'       => $token,
                'is_finished' => 0,
            ], ['address', 'products']);

        if (!$orders->count()) {
            abort(404);
        }

        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            $token = OrderHelper::getOrderSessionToken();

            $trashOrders = $this->app->make(OrderInterface::class)
                ->allBy([
                    'token'       => $token,
                    'is_finished' => 0,
                ], ['address']);

            foreach ($trashOrders as $trashOrder) {
                $trashOrder->delete();
                if ($trashOrder->address && $trashOrder->address->id) {
                    $trashOrder->address->delete();
                }
            }

            $order = $orders->first();
            $sessionCheckoutData = [
                'name'                      => $order->address->name,
                'email'                     => $order->address->email,
                'phone'                     => $order->address->phone,
                'address'                   => $order->address->address,
                'country'                   => $order->address->country,
                'state'                     => $order->address->state,
                'city'                      => $order->address->city,
                'zip_code'                  => $order->address->zip_code,
                'shipping_method'           => $order->shipping_method,
                'shipping_option'           => $order->shipping_option,
                'shipping_amount'           => $order->shipping_amount,
            ];
            $request->merge(['address' => $sessionCheckoutData]);
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        $orders->loadMissing([
            'products',
            'products.product',
            'products.product.variationInfo',
            'products.product.variationInfo.configurableProduct',
            'products.product.variationInfo.configurableProduct.tax',
        ]);

        Cart::instance('cart')->destroy();
        foreach ($orders as $order) {
            foreach ($order->products as $orderProduct) {
                $request->merge(['qty' => $orderProduct->qty]);

                $product = $orderProduct->product;
                if ($product) {
                    OrderHelper::handleAddCart($product, $request);
                }
            }
        }

        $products = Cart::instance('cart')->products();
        if ($products->count()) {
            $this->handleProcessOrder($products, $token, $sessionCheckoutData, $request);
        }

        return $this->app->make(BaseHttpResponse::class)
            ->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    /**
     * Append validation rules that apply to the request.
     *
     * @param array $rules
     * @return array
     */
    public function processCheckoutRulesRequest($rules)
    {
        unset($rules['shipping_method']);
        $stores = $this->getStoresInCart();
        foreach ($stores as $storeId => $storeName) {
            $rules["shipping_method.$storeId"] = 'required|' . Rule::in(ShippingMethodEnum::values());
            $rules["shipping_option.$storeId"] = 'required';
        }

        return $rules;
    }

    /**
     * Get all stores in cart, including products without stores
     *
     * @return array|Collection
     */
    protected function getStoresInCart()
    {
        $originalProducts = Cart::instance('cart')->products()->pluck('original_product');
        $storeIdsInCart = $originalProducts->pluck('store_id');
        $stores = $this->app->make(StoreInterface::class)->allBy([['id', 'IN', $storeIdsInCart]]);
        $storesInCart = collect([]);
        foreach ($originalProducts as $original) {
            if ($original->store_id) {
                if ($store = $stores->firstWhere('id', $original->store_id)) {
                    $storesInCart[$store->id] = $store->name;
                    continue;
                }
            }
            $storesInCart[0] = theme_option('site_title');
        }

        return $storesInCart;
    }

    /**
     * @param array $messages
     * @return array
     */
    public function processCheckoutMessagesRequest($messages)
    {
        $stores = $this->getStoresInCart();
        foreach ($stores as $storeId => $storeName) {
            $messages["shipping_method.$storeId.required"] = trans('plugins/marketplace::order.shipping_method_required',
                ['name' => $storeName]);
            $messages["shipping_method.$storeId.in"] = trans('plugins/marketplace::order.shipping_method_in',
                ['name' => $storeName]);
            $messages["shipping_option.$storeId.required"] = trans('plugins/marketplace::order.shipping_option_required',
                ['name' => $storeName]);
        }

        return $messages;
    }

    /**
     * @param OrderModel $order
     * @param Request $request
     * @return OrderModel
     */
    public function afterOrderStatusCompleted(OrderModel $order)
    {
        if (!$order) {
            return $order;
        }

        $order->loadMissing(['store', 'store.customer']);

        if ($order->store->id && $order->store->customer->id) {
            $customer = $order->store->customer;
            $vendorInfo = $customer->vendorInfo;
            if (!$vendorInfo->id) {
                $vendorInfo = $this->app->make(VendorInfoInterface::class)
                    ->createOrUpdate([
                        'customer_id' => $customer->id,
                    ]);
            }

            if ($vendorInfo->id) {
                $revenue = $this->app->make(RevenueInterface::class)->getFirstBy(['order_id' => $order->id]);

                $feePercentage = MarketplaceHelper::getSetting('fee_per_order', 0);

                $fee = $order->amount * ($feePercentage / 100);
                $amount = $order->amount - $fee;
                $currentBalance = $customer->balance;

                $amountByCurrency = $amount;
                $revenueAmount = $revenue ? $revenue->amount : 0;

                $data = [
                    'sub_amount'      => $order->amount,
                    'fee'             => $fee,
                    'amount'          => $amount,
                    'currency'        => get_application_currency()->title,
                    'current_balance' => $currentBalance,
                    'customer_id'     => $customer->getKey(),
                ];

                DB::beginTransaction();
                try {
                    if ($revenue) {
                        $amountByCurrency -= $revenueAmount;
                        $fee = 0;
                        $data['current_balance'] = $currentBalance - $revenueAmount;
                        $revenue->fill($data);
                        $revenue->save();
                    } else {
                        $this->app->make(RevenueInterface::class)->createOrUpdate(array_merge([
                            'order_id' => $order->id,
                        ], $data));

                        $vendorInfo->total_revenue += $amountByCurrency;
                    }
                    $vendorInfo->balance += $amountByCurrency;
                    $vendorInfo->total_fee += $fee;
                    $vendorInfo->save();

                    DB::commit();
                } catch (Throwable $th) {
                    DB::rollBack();

                    throw $th;
                }
            }

        }

        return $order;
    }
}
