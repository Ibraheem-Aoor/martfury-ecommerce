<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiTrait;
use Botble\Support\Http\Requests\Request;
use Illuminate\Routing\Controller;

class AuthenticationController extends Controller
{
    use ApiTrait;
    public function login(Request $request)
    {
        return $this->response(200 , ['gg' => $request->toArray] , 200);
    }
}
