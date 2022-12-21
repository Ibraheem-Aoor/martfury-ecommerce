<?php

namespace App\Console\Commands;

use App\Models\User;
use Botble\ACL\Models\User as ModelsUser;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Events\OrderCompletedEvent;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Eloquent\ShipmentRepository;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class OrderDeliveredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delevired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orders are Delevired and completed after 5 days of being proccessing';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $shipmentRepository;
    protected $shipmentHistoryRepository;
    public function __construct(ShipmentInterface $shipmentRepository , ShipmentHistoryInterface  $shipmentHistoryRepository)
    {
        parent::__construct();
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentHistoryRepository = $shipmentHistoryRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Order::query()->where('created_at' , '<=', Carbon::now()->subDays(5)->toDateTimeString())
            ->whereStatus(OrderStatusEnum::PROCESSING)->chunk(100 , function($orders){
            foreach($orders as $order)
            {
                try{
                $shipment = $this->shipmentRepository->findOrFail($order->shipment->id);
                $id = $shipment?->id;
                $this->shipmentRepository->createOrUpdate(['status' => ShippingStatusEnum::DELIVERED], compact('id'));
                $this->shipmentHistoryRepository->createOrUpdate([
                    'action'      => 'update_status',
                    'description' => trans('plugins/ecommerce::shipping.changed_shipping_status', [
                        'status' => ShippingStatusEnum::getLabel(ShippingStatusEnum::DELIVERED),
                    ]),
                    'shipment_id' => $shipment->id,
                    'order_id'    => $shipment->order_id,
                    'user_id'     => ModelsUser::where('super_user' , true)->first()->id ?? 0,
                ]);
                $order->status = OrderStatusEnum::COMPLETED;
                $order->save();
                event(new OrderCompletedEvent($order));
                }catch(Throwable $e)
                {
                            info($e);
                }
            }
        });
        info('Orders Deleivred');
    }
}
