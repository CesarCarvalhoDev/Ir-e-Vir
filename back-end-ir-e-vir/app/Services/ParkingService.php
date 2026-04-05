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

            // Buscar ou criar veículo
            $vehicle = Vehicle::firstOrCreate(
                ['plate' => $plate],
                [
                    'type' => 'CARRO',
                    'hasRegistration' => false,
                    'availableBalance' => 0
                ]
            );

            // Verificar se já existe permanência ativa
            $existeAtiva = Stay::where('vehicleId', $vehicle->id)
                ->where('status', 'ATIVA')
                ->lockForUpdate()
                ->exists();

            if ($existeAtiva) {
                throw new \DomainException('Veículo já possui permanência ativa');
            }

            $camera = Camera::findOrFail($cameraId);

            $agora = now();

            return Stay::create([
                'entry' => $agora,
                'exit' => $agora,
                'totalTime' => 0,
                'status' => 'ATIVA',
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
                ->where('status', 'ATIVA')
                ->lockForUpdate()
                ->first();

            if (!$stay) {
                throw new \DomainException('Nenhuma permanência ativa encontrada');
            }

            $saida = now();

            $tempoTotal = Carbon::parse($stay->entry)
                ->diffInMinutes($saida);

            $stay->update([
                'exit' => $saida,
                'totalTime' => $tempoTotal,
                'status' => 'FINALIZADA'
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
            ->map(function ($s) {
                return [
                    'entrada' => $s->entry,
                    'saida' => $s->exit,
                    'tempoTotal' => $s->totalTime,
                    'status' => $s->status,
                ];
            });
    }
}