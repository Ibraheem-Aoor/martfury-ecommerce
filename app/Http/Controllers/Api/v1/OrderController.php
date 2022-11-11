<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiTrait;
use App\Http\Resources\OrderResource;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Shipment;
use Illuminate\Routing\Controller;
use Throwable;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    use ApiTrait;


    public function index(Request $request)
    {
        $api_token = config('app.token');
        if($request->token == $api_token)
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
                        'last_page' => $orders->lastPage(),
                    ];
                    return $this->response(200 , $data , 'SUCESS') ;
                }catch(Throwable $ex)
                {
                    return $this->response(400 , [] , 'FAILED');
                }
        }else{
            $data = ['status' => false ];
            $message = 'Unauthenticated';
            return $this->response(419 , $data , $message);
        }
    }





}
