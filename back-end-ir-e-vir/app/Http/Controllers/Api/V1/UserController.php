<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\LinkVehicleToUser;
use App\Http\Requests\User\LinkVehicleToUserRequest;

#[OA\Tag(
    name: 'Users',
    description: 'Endpoints relacionados aos usuários'
)]
class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        summary: 'Lista usuários',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de usuários retornada com sucesso'
            )
        ]
    )]
    public function index()
    {
        //
    }

    #[OA\Post(
        path: '/api/users',
        summary: 'Criar usuário',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário criado com sucesso'
            )
        ]
    )]
    public function store(Request $request)
    {
        //
    }

    #[OA\Get(
        path: '/api/users/{id}',
        summary: 'Buscar usuário por ID',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do usuário',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer'
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário encontrado'
            ),
            new OA\Response(
                response: 404,
                description: 'Usuário não encontrado'
            )
        ]
    )]
    public function show(string $id)
    {
        //
    }

    #[OA\Put(
        path: '/api/users/{id}',
        summary: 'Atualizar usuário',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do usuário',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer'
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário atualizado com sucesso'
            )
        ]
    )]
    public function update(Request $request, string $id)
    {
        //
    }

    #[OA\Delete(
        path: '/api/users/{id}',
        summary: 'Remover usuário',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do usuário',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer'
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário removido com sucesso'
            )
        ]
    )]
    public function destroy(string $id)
    {
        //
    }

    #[OA\Post(
        path: '/api/users/{id}/vehicles',
        summary: 'Vincular veículo ao usuário',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do usuário',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer'
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['plate', 'type'],
                properties: [
                    new OA\Property(
                        property: 'plate',
                        type: 'string',
                        example: 'ABC1234'
                    ),
                    new OA\Property(
                        property: 'type',
                        type: 'string',
                        example: 'car'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Veículo vinculado com sucesso'
            ),
            new OA\Response(
                response: 400,
                description: 'Erro ao vincular veículo'
            )
        ]
    )]
    public function linkToVehicle(
        LinkVehicleToUserRequest $request,
        User $user,
        LinkVehicleToUser $service
    ) {
        $data = $request->validated();

        $plate = $data['plate'];
        $type = $data['type'];

        try {

            $vehicle = $service->execute(
                $plate,
                $type,
                $user
            );

            if ($vehicle) {

                return response()->json($vehicle, 200);

            }

        } catch (\Exception $ex) {

            return response()->json([
                'message' => 'Erro ao tentar vincular veiculo',
                'erro' => $ex->getMessage()
            ], 400);

        }
    }
}
