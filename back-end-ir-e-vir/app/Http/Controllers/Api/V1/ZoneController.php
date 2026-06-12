<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Zone\CreateZoneRequest;
use Illuminate\Http\Request;
use App\Models\Zone;
use App\Services\Zone\CreateZoneService;
use App\Services\Zone\GetAllZonesService;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetAllZonesService $getAllZonesService)
    {
        $zones = $getAllZonesService->execute();
        return response()->json($zones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateZoneRequest $request, CreateZoneService $createZoneService)
    {
        $zone = $createZoneService->execute($request->validated());
        return response()->json($zone, 201);
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
}
