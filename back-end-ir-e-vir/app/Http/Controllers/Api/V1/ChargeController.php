<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Charges\ChargeResource;
use App\Services\Charge\GenerateChargeService;
use App\Services\Charge\GetAllChargesService;
use App\Services\Charge\GetChargeByPlate;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetAllChargesService $service)
    {
        return $service->execute()->toResourceCollection(ChargeResource::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GenerateChargeService $service)
    {

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

    public function showByPlate(string $plate, GetChargeByPlate $service){
      return new ChargeResource($service->execute($plate));
    }
}
