<?php

use App\Http\Controllers\Api\V1\ChargeController;
use Illuminate\Support\Facades\Route;

Route::get('/charges', [ChargeController::class, 'index']);
Route::get('/charges/{plate}', [ChargeController::class, 'showByPlate']);
