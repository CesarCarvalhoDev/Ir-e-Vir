<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\LinkVehicleToUser;
use App\Http\Requests\User\LinkVehicleToUserRequest;
use App\Services\User\GetStaysByUser;

#[OA\Tag(
    name: 'Users',
    description: 'Endpoints relacionados aos usuários'
)]
class UserController extends Controller
{

    public function index()
    {

    }

    public function store(Request $request)
    {
        //
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

    public function linkToVehicle(LinkVehicleToUserRequest $request,User $user,LinkVehicleToUser $service) {
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

    public function showUserStays(GetStaysByUser $getStaysByUser, User $user){
        try {
            $stays = $getStaysByUser->execute($user);

            return response()->json($stays, 200);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }
}
