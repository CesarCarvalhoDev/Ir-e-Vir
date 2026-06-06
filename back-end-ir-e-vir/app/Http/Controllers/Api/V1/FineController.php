<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fines\FineResource;
use App\Models\Fine;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function index()
    {
        return FineResource::collection(
            Fine::with(['user', 'stay.vehicle'])->latest()->get()
        );
    }

    public function mine(Request $request)
    {
        $fines = Fine::with(['user', 'stay.vehicle'])
            ->where(function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->orWhereHas('stay.vehicle.users', function ($subQuery) use ($request) {
                        $subQuery->where('users.id', $request->user()->id);
                    });
            })
            ->latest()
            ->get();

        return FineResource::collection($fines);
    }
}
