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

#[OA\Tag(
    name: 'Charges',
    description: 'Endpoints relacionados às cobranças'
)]
class ChargeController extends Controller
{
    #[OA\Get(
        path: '/api/charges',
        summary: 'Lista todas as cobranças',
        tags: ['Charges'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de cobranças retornada com sucesso'
            )
        ]
    )]
    public function index(GetAllChargesService $service)
    {
        return $service->execute()->toResourceCollection(ChargeResource::class);
    }

    #[OA\Post(
        path: '/api/charges',
        summary: 'Gerar uma nova cobrança',
        tags: ['Charges'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Cobrança gerada com sucesso'
            ),
            new OA\Response(
                response: 400,
                description: 'Erro ao gerar cobrança'
            )
        ]
    )]
    public function store(
        Stay $stay,
        GenerateChargeService $generateChargeService,
        CalculateTariffValue $calculateTariffValue
    ) {
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

    #[OA\Get(
        path: '/api/charges/{plate}',
        summary: 'Buscar cobrança pela placa',
        tags: ['Charges'],
        parameters: [
            new OA\Parameter(
                name: 'plate',
                description: 'Placa do veículo',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cobrança encontrada'
            ),
            new OA\Response(
                response: 404,
                description: 'Cobrança não encontrada'
            )
        ]
    )]
    public function showByPlate(
        string $plate,
        GetChargeByPlate $service
    ) {
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
