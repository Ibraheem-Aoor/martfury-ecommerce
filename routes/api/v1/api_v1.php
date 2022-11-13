<?php

use App\Http\Controllers\Api\v1\AuthenticationController;
use App\Http\Controllers\Api\v1\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('orders' , [OrderController::class , 'index'] );
Route::put('orders/confirm' , [OrderController::class , 'update']);
