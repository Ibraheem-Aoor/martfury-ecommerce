<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiTrait;
use App\Http\Requests\Api\OrderApiRequest;
use App\Http\Resources\OrderResource;
use Botble\ACL\Models\User;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Shipment;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Routing\Controller;
use Throwable;
use Illuminate\Http\Request;
use EmailHandler;
use Botble\Ecommerce\Events\OrderConfirmedEvent;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\ShipmentHistory;
use Botble\Ecommerce\Repositories\Eloquent\OrderHistoryRepository;
use OrderHelper;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Illuminate\Support\Facades\Validator;

use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\StoreLocatorInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;

class OrderController extends Controller
{
    use ApiTrait;

    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;


    /**
     * @var ShipmentInterface
     */
    protected $shipmentRepository;

    /**
     * @var PaymentInterface
     */
    protected $paymentRepository;

    /**
     * @var StoreLocatorInterface
     */
    protected $storeLocatorRepository;

    /**
     * @var OrderProductInterface
     */
    protected $orderProductRepository;


    /**
     * @var ShipmentHistoryRepository
     */
    protected $shipmentHistoryRepository;

    /**
     * @var $apiToken.
     */
    protected $api_token;

    public function __construct(
        OrderInterface $orderRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ShipmentInterface $shipmentRepository,
        PaymentInterface $paymentRepository,
        StoreLocatorInterface $storeLocatorRepository,
        ShipmentHistoryInterface $shipmentHistoryRepository

    ) {
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->paymentRepository = $paymentRepository;
        $this->storeLocatorRepository = $storeLocatorRepository;
        $this->shipmentHistoryRepository = $shipmentHistoryRepository;
        $this->api_token =  config('app.token');;
    }



    public function index(Request $request)
    {
        if($request->token == $this->api_token)
        {
            try{
                $orders = Order::query()
                ->whereHas('payment' , function($payment)
                {
                    $payment->where('status' , PaymentStatusEnum::COMPLETED);
                })
                ->where('status' , '!=' ,  OrderStatusEnum::CANCELED)
                ->with([
                    'shipment' ,
                    'products' ,
                    'address' ,
                    ])
                    ->orderByDesc('created_at')
                    ->paginate(50);
                    $data = [
                        'status' => 200,
                        'orders' => OrderResource::collection($orders),
                        'last_page' => $orders->lastPage(),
                    ];
                    return $this->response(200 , $data , 'SUCESS') ;
                }catch(Throwable $ex)
                {
                    return $this->response(400 , [] , 'FAILED');
                }
        }else{
            return $this->unAuthorizedResponse();
        }
    }


    /**
     * Update The given order.
     * This method confirms the order and create a shipment for it and return order shipment
     */

    public function update(OrderApiRequest $request)
    {
        if($request->token == $this->api_token)
        {
            $data = [];
            $error_no = 400;
            $order = Order::query()->findOrFail($request->order_id);
            if($this->confirmOrder($order->id))
            {
                if($shipment =  $this->createShipment($order))
                {
                    $this->updateTrackData($request);
                    $message = 'Order Confirmed Successfully';
                    $error_no = 200;
                    $data['shipment'] = $shipment;
                    $data['status'] = 200;
                }else{
                    $error_no = 500;
                    $message = 'Order Crate Shipment Failed!';
                }
            }else{
                $message = "Order Confirmation Failed";
                $data['message'] = $message;
            }

            return $this->response($error_no , $data ,$message);
        }
        return $this->unAuthorizedResponse();
    }




    public function confirmOrder($order_id)
    {
        try{
            $order = Order::query()->findOrFail($order_id);
            $order->is_confirmed = 1;
            if ($order->status == OrderStatusEnum::PENDING) {
                $order->status = OrderStatusEnum::PROCESSING;
            }

            $this->orderRepository->createOrUpdate($order);

            $user = User::where('super_user' , true)->first();

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'confirm_order',
                'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
                'order_id'    => $order->id,
                'user_id'     => $user->id,
            ]);

            $payment = $this->paymentRepository->getFirstBy(['order_id' => $order->id]);

            if ($payment) {
                $payment->user_id = $user->id;
                $payment->save();
            }
            event(new OrderConfirmedEvent($order, $user));

            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('order_confirm')) {
                OrderHelper::setEmailVariables($order);
                $mailer->sendUsingTemplate(
                    'order_confirm',
                    $order->user->email ?: $order->address->email
                );
            }
            return true;
        }catch(Throwable $e)
        {
            return false;
        }
    }



    public function createShipment($order)
    {
        try
        {
            $weight = $order->getTotalWeight();
            $user = User::where('super_user' , true)->first();
            $shipment = [
                'order_id'   => $order->id,
                'user_id'    => $user->id,
                'weight'     => $weight,
                'cod_status' => 'pending',
                'type'       => $order->shipping_method,
                'status'     => ShippingStatusEnum::DELIVERING,
                'price'      => $order->shipping_amount,
            ];
            $message = trans('plugins/ecommerce::order.order_was_sent_to_shipping_team');
            $created_shipment = $this->shipmentRepository->createOrUpdate($shipment);
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('customer_delivery_order')) {
                OrderHelper::setEmailVariables($order);
                $mailer->sendUsingTemplate(
                    'customer_delivery_order',
                    $order->user->email ?: $order->address->email
                );
            }

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'create_shipment',
                'description' => $message . ' ' . trans('plugins/ecommerce::order.by_username'),
                'order_id'    => $order->id,
                'user_id'     => $user->id,
            ]);

            $this->shipmentHistoryRepository->createOrUpdate([
                'action'      => 'create_from_order',
                'description' => trans('plugins/ecommerce::order.shipping_was_created_from'),
                'shipment_id' => $created_shipment->id,
                'order_id'    => $order->id,
                'user_id'     => $user->id,
            ]);
            return $created_shipment;
        }catch(Throwable $e)
        {
            return false;
        }
    }



    /**
     * Update Order Track Data
     */

    protected function updateTrackData($request)
    {
        $order = Order::query()->findOrFail($request->order_id);
        $order->update([
            'shipping_company_name'  => $request->shipping_company_name,
            'shipping_tracking_id'   => $request->shipping_tracking_id,
            'shipping_tracking_link'  => $request->shipping_tracking_link,
        ]);
        $mesasge = "Order Track Data Updated Successfully";
        $eror_no = 200;
        $data['status'] = $eror_no;
        return $this->response($eror_no , $data , $mesasge);
    }

}
