<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LinkVehicleToUserRequest;
use App\Models\User;
use App\Services\User\LinkVehicleToUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function linkToVehicle(LinkVehicleToUserRequest $request, User $user, LinkVehicleToUser $service)
    {
        $data = $request->validated();
        $plate = $data['plate'];
        $type = $data['type'];

        try {
           $vehicle = $service->execute($plate, $type, $user);
           if($vehicle){
                return response()->json($vehicle, 200);
           }   
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Erro ao tentar vincular veiculo', 'erro' => $ex->getMessage()]);
        }
    }
}
