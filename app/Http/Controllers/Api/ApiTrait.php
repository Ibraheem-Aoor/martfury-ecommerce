<?php
namespace App\Http\Controllers\Api;
trait ApiTrait
{
    public function response($error_no  , $data = []  , $message)
    {
        $data['message']  = $message;
        return response()->json($data  , $error_no);
    }
}
