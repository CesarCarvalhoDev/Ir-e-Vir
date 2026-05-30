<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

use App\Http\Controllers\Controller;

use App\Http\Requests\Stay\StoreEntryStayRequest;
use App\Http\Requests\Stay\StoreExitStayRequest;

use App\Services\Stay\GetAllStaysService;
use App\Services\Stay\StoreEntryStayService;
use App\Services\Stay\StoreExitStayService;

#[OA\Tag(
    name: 'Stays',
    description: 'Endpoints relacionados às estadias'
)]
class StayController extends Controller
{
    #[OA\Get(
        path: '/api/stays',
        summary: 'Lista todas as estadias',
        tags: ['Stays'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de estadias retornada com sucesso'
            )
        ]
    )]
    public function index(
        GetAllStaysService $service
    ) {
        return response()->json(
            $service->execute(),
            200
        );
    }

    #[OA\Post(
        path: '/api/stays/entry',
        summary: 'Registrar entrada de veículo',
        tags: ['Stays'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['plate'],
                properties: [
                    new OA\Property(
                        property: 'plate',
                        type: 'string',
                        example: 'ABC1234'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Entrada registrada com sucesso'
            ),
            new OA\Response(
                response: 400,
                description: 'Erro ao registrar entrada'
            )
        ]
    )]
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

    #[OA\Post(
        path: '/api/stays/exit',
        summary: 'Registrar saída de veículo',
        tags: ['Stays'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['plate'],
                properties: [
                    new OA\Property(
                        property: 'plate',
                        type: 'string',
                        example: 'ABC1234'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Saída registrada com sucesso'
            ),
            new OA\Response(
                response: 400,
                description: 'Erro ao registrar saída'
            )
        ]
    )]
    public function storeExit(
        StoreExitStayRequest $request,
        StoreExitStayService $service
    ) {
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
