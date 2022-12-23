<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiTrait;
use Botble\Ecommerce\Models\Discount;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class DiscountController extends Controller
{

    use ApiTrait;

    /**
     * @var $apiToken.
     */
    protected $api_token;

    public function __construct(Request $request)
    {
        $this->api_token =  config('app.token');
    }


    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->token == $this->api_token)
        {
            $data['coupon_codes'] = Discount::query()->pluck('code');
            $data['status'] = 200;
            return $this->response(200, $data, 'SUCCESS');
        }else
        {
            return $this->unAuthorizedResponse();
        }
    }

    /**
     *  Store Discount Coupons Incoming from our platform
     * @return void
     */
    public function store(Request $request)
    {
        if ($request->token == $this->api_token) {
            try{
                DB::beginTransaction();
                $created_coupon = Discount::query()->create($request->input());
                DB::commit();
                $message = 'Created Successfully';
                $erro_no = 200;
                $data = $created_coupon;
            }catch(QueryException $e)
            {
                DB::rollBack();
                if($e->errorInfo[1] == 1062)
                {
                    $message = "Coupon Already Exists!";
                    $erro_no = 409;
                    $data = [];
                }else{
                    DB::rollBack();
                    $message = 'ERORR';
                    $erro_no = 400;
                    $data = [];
                }
            }
            catch(Throwable $e)
            {
                DB::rollBack();
                $message = 'ERORR';
                $error_no = 400;
                $data = [];
            }
            $data['status'] = $erro_no;
            return $this->response($erro_no, $data, $message);
        }else{
            return $this->unAuthorizedResponse();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
