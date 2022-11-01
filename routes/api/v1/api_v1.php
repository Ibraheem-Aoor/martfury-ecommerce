<?php

use App\Http\Controllers\Api\v1\AuthenticationController;
use App\Http\Controllers\Api\v1\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth' , 'middleware' => 'guest:sanctum'] , function()
{
    Route::post('login' , [AuthenticationController::class , 'login']);
});

Route::group(['middleware' => 'auth:sanctum'] , function()
{

});

Route::apiResource('orders' , OrderController::class );
