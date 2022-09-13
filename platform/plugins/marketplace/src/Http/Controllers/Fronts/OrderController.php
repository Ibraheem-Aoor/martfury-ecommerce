<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\UpdateOrderRequest;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Marketplace\Tables\OrderTable;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use EmailHandler;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MarketplaceHelper;
use OrderHelper;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class OrderController extends BaseController
{
    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderAddressRepository;

    /**
     * @var PaymentInterface
     */
    protected $paymentRepository;

    /**
     * @param OrderInterface $orderRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     * @param OrderAddressInterface $orderAddressRepository
     * @param PaymentInterface $paymentRepository
     */
    public function __construct(
        OrderInterface $orderRepository,
        OrderHistoryInterface $orderHistoryRepository,
        OrderAddressInterface $orderAddressRepository,
        PaymentInterface $paymentRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->paymentRepository = $paymentRepository;

        Assets::setConfig(config('plugins.marketplace.assets', []));
    }

    /**
     * @param Request $request
     * @param OrderTable $table
     * @return JsonResponse|View|Response
     * @throws Throwable
     */
    public function index(OrderTable $table)
    {
        page_title()->setTitle(__('Orders'));

        $orders = auth('customer')->user()->store->orders()->get();

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'), compact('orders'));
    }

    /**
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        $order = $this->orderRepository
            ->getModel()
            ->where('id', $id)
            ->with(['products', 'user'])
            ->firstOrFail();

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        page_title()->setTitle(trans('plugins/ecommerce::order.edit_order', ['code' => get_order_code($id)]));

        $weight = 0;
        foreach ($order->products as $product) {
            if ($product && $product->weight) {
                $weight += $product->weight;
            }
        }

        $defaultStore = get_primary_store_locator();

        return MarketplaceHelper::view('dashboard.orders.edit', compact('order', 'weight', 'defaultStore'));
    }

    /**
     * @param int $id
     * @param UpdateOrderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, UpdateOrderRequest $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->createOrUpdate($request->input(), ['id' => $id]);

        event(new UpdatedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));

        return $response
            ->setPreviousUrl(route('orders.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        try {
            $this->orderRepository->deleteBy(['id' => $id]);
            event(new DeletedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $order = $this->orderRepository->findOrFail($id);

            if ($order->store_id != auth('customer')->user()->store->id) {
                abort(404);
            }

            $this->orderRepository->delete($order);
            event(new DeletedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $orderId
     * @return BinaryFileResponse
     */
    public function getGenerateInvoice($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $invoice = OrderHelper::generateInvoice($order);

        return response()->download($invoice)->deleteFileAfterSend();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postConfirm(Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($request->input('order_id'));

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $order->is_confirmed = 1;
        if ($order->status == OrderStatusEnum::PENDING) {
            $order->status = OrderStatusEnum::PROCESSING;
        }

        $this->orderRepository->createOrUpdate($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'confirm_order',
            'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
            'order_id'    => $order->id,
            'user_id'     => 0,
        ]);

        $payment = $this->paymentRepository->getFirstBy(['order_id' => $order->id]);

        if ($payment) {
            $payment->user_id = 0;
            $payment->save();
        }

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('order_confirm')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'order_confirm',
                $order->user->email ?: $order->address->email
            );
        }

        return $response->setMessage(trans('plugins/ecommerce::order.confirm_order_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postResendOrderConfirmationEmail($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $result = OrderHelper::sendOrderConfirmationEmail($order);

        if (!$result) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.error_when_sending_email'));
        }

        return $response->setMessage(trans('plugins/ecommerce::order.sent_confirmation_email_success'));
    }

    /**
     * @param int $id
     * @param AddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postUpdateShippingAddress($id, AddressRequest $request, BaseHttpResponse $response)
    {
        $address = $this->orderAddressRepository->createOrUpdate($request->input(), compact('id'));

        if (!$address) {
            abort(404);
        }

        if ($address->order->status == OrderStatusEnum::CANCELED) {
            abort(401);
        }

        return $response
            ->setData([
                'line'   => view('plugins/ecommerce::orders.shipping-address.line', compact('address'))->render(),
                'detail' => view('plugins/ecommerce::orders.shipping-address.detail', compact('address'))->render(),
            ])
            ->setMessage(trans('plugins/ecommerce::order.update_shipping_address_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postCancelOrder($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::CANCELED, 'is_confirmed' => true],
            compact('id'));

        foreach ($order->products as $orderProduct) {
            $product = $orderProduct->product;
            $product->quantity += $orderProduct->qty;

            if ($product->is_variation) {
                $originalProduct = $product->original_product;
                $originalProduct->quantity += $orderProduct->qty;
                $originalProduct->save();
            }

            $product->save();
        }

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'cancel_order',
            'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
            'order_id'    => $order->id,
            'user_id'     => 0,
        ]);

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('customer_cancel_order')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'customer_cancel_order',
                $order->user->email ?: $order->address->email
            );
        }

        return $response->setMessage(trans('plugins/ecommerce::order.customer.messages.cancel_success'));
    }
}
