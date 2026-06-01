<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use App\Models\Stay;
use App\Http\Controllers\Controller;
use App\Http\Resources\Charges\ChargeResource;
use App\Services\Charge\GenerateChargeService;
use App\Services\Charge\GetAllChargesService;
use App\Services\Charge\GetChargeByPlate;
use App\Services\Tariff\CalculateTariffValue;

class ChargeController extends Controller
{
    public function index(GetAllChargesService $service)
    {
        return $service->execute()->toResourceCollection(ChargeResource::class);
    }

    public function store(Stay $stay,GenerateChargeService $generateChargeService,CalculateTariffValue $calculateTariffValue) {
        try {
            $totalValue = $calculateTariffValue->execute($stay);

            $charge = $generateChargeService->execute(
                $stay,
                $totalValue
            );

            return response()->json(
                new ChargeResource($charge),
                201
            );

        } catch (\Exception $ex) {

            return response()->json([
                'message' => 'Erro ao tentar gerar cobrança',
                'errors' => $ex->getMessage()
            ], 400);
        }
    }

    public function showByPlate(string $plate,GetChargeByPlate $service) {
        return new ChargeResource(
            $service->execute($plate)
        );
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
