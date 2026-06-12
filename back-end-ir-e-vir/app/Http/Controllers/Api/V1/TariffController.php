<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tariff\StoreTariffRequest;
use App\Http\Resources\Tariffs\TariffResource;
use App\Services\Tariff\CreateTariffService;
use App\Services\Tariff\GetAllTariffsService;

class TariffController extends Controller
{
    public function index(GetAllTariffsService $service)
    {
        return TariffResource::collection($service->execute());
    }

    public function store(
        StoreTariffRequest $request,
        CreateTariffService $service
    ) {
        return response()->json(
            new TariffResource($service->execute($request->validated())),
            201
        );
    }
}
