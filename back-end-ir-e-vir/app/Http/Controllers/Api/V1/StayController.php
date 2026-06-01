<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\Stay\StoreEntryStayRequest;
use App\Http\Requests\Stay\StoreExitStayRequest;

use App\Services\Stay\GetAllStaysService;
use App\Services\Stay\StoreEntryStayService;
use App\Services\Stay\StoreExitStayService;

class StayController extends Controller
{
    public function index(
        GetAllStaysService $service
    ) {
        return response()->json(
            $service->execute(),
            200
        );
    }

    public function storeEntry(StoreEntryStayRequest $request, StoreEntryStayService $service) {
        try {

            $stay = $service->execute(
                $request->validated()
            );

            return response()->json(
                $stay,
                201
            );

        } catch (\Exception $ex) {

            return response()->json([
                'error' => $ex->getMessage()
            ], 400);
        }
    }

    public function storeExit(StoreExitStayRequest $request,StoreExitStayService $service) {
        try {

            $response = $service->execute(
                $request->validated()
            );

            return response()->json(
                $response,
                200
            );

        } catch (\Exception $ex) {

            return response()->json([
                'error' => $ex->getMessage()
            ], 400);

        }
    }
}
