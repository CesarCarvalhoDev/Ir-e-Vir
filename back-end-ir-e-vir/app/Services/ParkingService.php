<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Camera;
use App\Models\Stay;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParkingService
{
    public function registerEntrance(string $plate, int $cameraId)
    {
        return DB::transaction(function () use ($plate, $cameraId) {

            // Find or create vehicle
            $vehicle = Vehicle::firstOrCreate(
                ['plate' => $plate],
                [
                    'type' => 'CAR',
                    'hasRegistration' => false,
                    'availableBalance' => 0
                ]
            );

            // Check if there is already an active stay
            $hasActiveStay = Stay::where('vehicleId', $vehicle->id)
                ->where('status', 'ACTIVE')
                ->lockForUpdate()
                ->exists();

            if ($hasActiveStay) {
                throw new \DomainException('Veículo já possui permanência ativa');
            }

            $camera = Camera::findOrFail($cameraId);

            $currentTime = now();

            return Stay::create([
                'entry' => $currentTime,
                'exit' => $currentTime,
                'totalTime' => 0,
                'status' => 'ACTIVE',
                'vehicleId' => $vehicle->id,
                'zoneId' => $camera->zoneId
            ]);
        });
    }

    public function registerExit(string $plate)
    {
        return DB::transaction(function () use ($plate) {

            $vehicle = Vehicle::where('plate', $plate)->first();

            if (!$vehicle) {
                throw new \DomainException('Veículo não encontrado');
            }

            $stay = Stay::where('vehicleId', $vehicle->id)
                ->where('status', 'ACTIVE')
                ->lockForUpdate()
                ->first();

            if (!$stay) {
                throw new \DomainException('Nenhuma permanência ativa encontrada');
            }

            $exitTime = now();

            $totalTime = Carbon::parse($stay->entry)
                ->diffInMinutes($exitTime);

            $stay->update([
                'exit' => $exitTime,
                'totalTime' => $totalTime,
                'status' => 'FINISHED'
            ]);

            return $stay;
        });
    }

    public function listByPlate(string $plate)
    {
        $vehicle = Vehicle::where('plate', $plate)->first();

        if (!$vehicle) {
            return [];
        }

        return Stay::where('vehicleId', $vehicle->id)
            ->orderByDesc('entry')
            ->get()
            ->map(function ($stay) {
                return [
                    'entry' => $stay->entry,
                    'exit' => $stay->exit,
                    'totalTime' => $stay->totalTime,
                    'status' => $stay->status,
                ];
            });
    }
}
