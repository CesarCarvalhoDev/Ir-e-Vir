<?php

namespace App\Services\Zone;

use App\Models\Zone;
use Illuminate\Support\Facades\DB;

class CreateZoneService
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Zone::create([
                'name' => $data['name'],
                'maximum_time' => $data['maximum_time'],
            ]);
        });
    }
}
