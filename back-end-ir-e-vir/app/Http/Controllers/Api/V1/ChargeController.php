<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Charges\ChargeResource;
use App\Models\Charge;
use App\Models\Stay;
use App\Services\Charge\GenerateChargeService;
use App\Services\Charge\GetAllChargesService;
use App\Services\Charge\GetChargeByPlate;
use App\Services\Tariff\CalculateTariffValue;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetAllChargesService $service)
    {
        // Get only charges for vehicles owned by the authenticated user
        $user = auth()->user();
        $vehicleIds = $user->vehicles()->pluck('id');
        
        $charges = \App\Models\Charge::query()
            ->whereHas('stay', function ($query) use ($vehicleIds) {
                $query->whereIn('vehicle_id', $vehicleIds);
            })
            ->get();

        return ChargeResource::collection($charges);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Stay $stay, GenerateChargeService $generateChargeService, CalculateTariffValue $calculateTariffValue)
    {
        try {
            $totalValue = $calculateTariffValue->execute($stay);
            $charge = $generateChargeService->execute($stay, $totalValue);
            return response()->json(new ChargeResource($charge), 201);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Erro ao tentar gerar cobrança', 'errors' => $ex->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showByPlate(string $plate, GetChargeByPlate $service)
    {
        // Get only charges for vehicles owned by the authenticated user
        $user = auth()->user();
        
        $charge = \App\Models\Charge::query()
            ->whereHas('stay.vehicle', function ($query) use ($plate, $user) {
                $query->where('plate', $plate)
                    ->where('user_id', $user->id);
            })
            ->first();

        if (!$charge) {
            return response()->json(['message' => 'Charge not found'], 404);
        }

        return new ChargeResource($charge);
    }
}
