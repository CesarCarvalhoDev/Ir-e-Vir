<?php

namespace App\Services\Stay;

use App\Models\Stay;
use Carbon\Carbon;
use Exception;

class StoreExitStayService
{
    public function execute(array $data)
    {
        $stay = Stay::where('vehicle_id', $data['vehicle_id'])
            ->whereNull('exit')
            ->where('status', 'ACTIVE')
            ->first();

        if (!$stay) {
            throw new Exception('Nenhuma permanência aberta encontrada.');
        }

        $entry = Carbon::parse($stay->entry);
        $exit = Carbon::parse($data['exit']);

        $totalMinutes = $entry->diffInMinutes($exit);

        $stay->update([
            'exit' => $data['exit'],
            'total_time' => $totalMinutes,
            'status' => 'CLOSED',
        ]);

        return $stay;
    }
}
