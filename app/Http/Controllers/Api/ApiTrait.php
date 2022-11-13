<?php
namespace App\Http\Controllers\Api;
trait ApiTrait
{
    public function response($error_no  , $data = []  , $message)
    {
        $data['message']  = $message;
        return response()->json($data  , $error_no);
    }


    public function unAuthorizedResponse()
    {
        $data = ['status' => false ];
        $message = 'Unauthenticated';
        return $this->response(419 , $data , $message);
    }


}
