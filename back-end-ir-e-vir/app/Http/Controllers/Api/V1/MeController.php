<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LinkVehicleToUserRequest;
use App\Http\Resources\Charges\ChargeResource;
use App\Http\Resources\Stays\StayResource;
use App\Http\Resources\Vehicles\VehicleResorce;
use App\Models\Charge;
use App\Services\Charge\PayChargeWithWalletService;
use App\Services\User\LinkVehicleToUser;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function vehicles(Request $request)
    {
        return VehicleResorce::collection($request->user()->vehicles()->get());
    }

    public function stays(Request $request)
    {
        $stays = $request->user()
            ->vehicles()
            ->with('stays')
            ->get()
            ->flatMap(fn ($vehicle) => $vehicle->stays)
            ->values();

        return StayResource::collection($stays);
    }

    public function charges(Request $request)
    {
        $charges = Charge::with('stay.vehicle')
            ->whereHas('stay.vehicle.users', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->get();

        return ChargeResource::collection($charges);
    }

    public function linkVehicle(
        LinkVehicleToUserRequest $request,
        LinkVehicleToUser $service
    ) {
        $vehicle = $service->execute(
            $request->validated('plate'),
            $request->validated('type'),
            $request->user()
        );

        return response()->json(new VehicleResorce($vehicle), 200);
    }

    public function payCharge(
        Request $request,
        Charge $charge,
        PayChargeWithWalletService $service
    ) {
        $belongsToUser = Charge::where('id', $charge->id)
            ->whereHas('stay.vehicle.users', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->exists();

        if (!$belongsToUser) {
            return response()->json([
                'message' => 'Cobrança não pertence ao usuário autenticado.',
            ], 403);
        }

        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json([
                'message' => 'Usuário não possui carteira.',
            ], 400);
        }

        if ($charge->status === Charge::STATUS_PAID) {
            return response()->json([
                'message' => 'Esta cobrança já foi paga.',
            ], 400);
        }

        try {
            $payment = $service->execute($charge, $wallet);

            return response()->json([
                'message' => 'Cobrança paga com sucesso.',
                'charge' => new ChargeResource($charge->fresh()),
                'payment' => [
                    'id' => $payment->id,
                    'paid_value' => $payment->paid_value,
                    'payment_date' => $payment->payment_date,
                    'status' => $payment->status,
                ],
                'remaining_balance' => $wallet->fresh()->balance,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Erro ao pagar cobrança.',
                'errors' => $ex->getMessage(),
            ], 400);
        }
    }
}
