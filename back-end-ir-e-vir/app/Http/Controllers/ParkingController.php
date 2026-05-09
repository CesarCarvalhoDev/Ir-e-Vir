<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ParkingService;

class ParkingController extends Controller
{
    public function __construct(
        protected ParkingService $service
    ) {}

    public function entry(Request $request)
    {
        try {
            $request->validate([
                'plate' => 'required|string',
                'cameraId' => 'required|integer|exists:cameras,id'
            ]);

            $result = $this->service->registerEntrance(
                $request->plate,
                $request->cameraId
            );

            return response()->json($result, 201);
        } catch (\DomainException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function exit(Request $request)
    {
        try {
            $request->validate([
                'plate' => 'required|string'
            ]);

            $result = $this->service->registerExit($request->plate);

            return response()->json($result);
        } catch (\DomainException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function list($plate)
    {
        return response()->json(
            $this->service->listByPlate($plate)
        );
    }
}
