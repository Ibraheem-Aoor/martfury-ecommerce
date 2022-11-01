<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiTrait;
use App\Http\Resources\OrderResource;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Shipment;
use Illuminate\Routing\Controller;
use Throwable;

class OrderController extends Controller
{
    use ApiTrait;

    public function index()
    {
        try{
            $orders = Order::query()
                        ->with([
                                'shipment' ,
                                'products' ,
                                'address' ,
                                'payment'])
                        ->orderByDesc('created_at')
                        ->paginate(50);
            $data = [
                'status' => 200,
                'orders' => OrderResource::collection($orders),
            ];
            return $this->response(200 , $data , 'SUCESS') ;
        }catch(Throwable $ex)
        {
            return $this->response(400 , [] , 'FAILED');
        }
    }





}
