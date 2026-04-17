<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChargeController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    // Rotas de autenticação (públicas)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Rotas protegidas por autenticação
    Route::middleware('auth:sanctum')->group(function () {
        // Autenticação
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Cobranças
        Route::get('/charges', [ChargeController::class, 'index']);
        Route::post('/charges/{stay}', [ChargeController::class, 'store']);
        Route::get('/charges/plate/{plate}', [ChargeController::class, 'showByPlate']);
    });
});
