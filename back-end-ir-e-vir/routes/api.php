<?php

use App\Http\Controllers\Api\V1\StayController;
use App\Http\Controllers\Api\V1\ChargeController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\ZoneController;
use Illuminate\Support\Facades\Route;


Route::apiResource('/zones', ZoneController::class);
Route::get('/vehicles', [VehicleController::class, 'index']);

Route::get('/stays', [StayController::class, 'index']);
Route::post('/stays/entry', [StayController::class, 'storeEntry']);
Route::post('/stays/exit', [StayController::class, 'storeExit']);
Route::post('/charges/{stay}', [ChargeController::class, 'store']);
Route::get('/charges', [ChargeController::class, 'index']);
Route::get('/charges/{plate}', [ChargeController::class, 'showByPlate']);
Route::post('/charges/{charge}/pay/{user}', [ChargeController::class, 'pay']);
Route::post('/user/{user}/vehicles', [UserController::class, 'linkToVehicle']);
Route::get('/user/{user}/stays', [UserController::class, 'showUserStays']);

