<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('parking')->group(function () {

    Route::post('/entrada', [ParkingController::class, 'entrada']);

    Route::post('/saida', [ParkingController::class, 'saida']);

    Route::get('/permanencias/{placa}', [ParkingController::class, 'listar']);

});