<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StayController;
use App\Http\Controllers\Api\V1\ChargeController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\ZoneController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/me/vehicles', [MeController::class, 'vehicles']);
    Route::post('/me/vehicles', [MeController::class, 'linkVehicle']);
    Route::get('/me/stays', [MeController::class, 'stays']);
    Route::get('/me/charges', [MeController::class, 'charges']);
    Route::post('/me/charges/{charge}/pay', [MeController::class, 'payCharge']);

    Route::middleware('role:admin')->prefix('/admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::apiResource('/zones', ZoneController::class);
        Route::get('/vehicles', [VehicleController::class, 'index']);

        Route::get('/stays', [StayController::class, 'index']);
        Route::post('/stays/entry', [StayController::class, 'storeEntry']);
        Route::post('/stays/exit', [StayController::class, 'storeExit']);

        Route::get('/charges', [ChargeController::class, 'index']);
        Route::get('/charges/{plate}', [ChargeController::class, 'showByPlate']);
        Route::post('/charges/{stay}', [ChargeController::class, 'store']);
        Route::post('/charges/{charge}/pay/{user}', [ChargeController::class, 'pay']);

        Route::post('/user/{user}/vehicles', [UserController::class, 'linkToVehicle']);
        Route::get('/user/{user}/stays', [UserController::class, 'showUserStays']);
    });
});
