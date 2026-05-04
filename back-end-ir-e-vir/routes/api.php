<?php

use App\Http\Controllers\Api\V1\ChargeController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/charges/{stay}', [ChargeController::class, 'store']);
Route::get('/charges', [ChargeController::class, 'index']);
Route::get('/charges/{plate}', [ChargeController::class, 'showByPlate']);
Route::post('/user/{user}/vehicles', [UserController::class, 'linkToVehicle']);

