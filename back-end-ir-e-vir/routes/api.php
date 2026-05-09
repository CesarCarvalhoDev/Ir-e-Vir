<?php

use App\Http\Controllers\Api\V1\ChargeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingController;

Route::post('/charges/{stay}', [ChargeController::class, 'store']);
Route::get('/charges', [ChargeController::class, 'index']);
Route::get('/charges/{plate}', [ChargeController::class, 'showByPlate']);
