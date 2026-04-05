<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstacionamentoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('estacionamento')->group(function () {

    Route::post('/entrada', [EstacionamentoController::class, 'entrada']);

    Route::post('/saida', [EstacionamentoController::class, 'saida']);

    Route::get('/permanencias/{placa}', [EstacionamentoController::class, 'listar']);

});