<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Vehicle;
use App\Models\Stay;
use App\Models\Charge;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $zones = Zone::factory()->count(5)->create();

        $zones->each(function ($zone) {
            \App\Models\Tariff::factory()->create([
                'zone_id' => $zone->id,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(10),
                'active' => true,
            ]);
        });

        $users = User::factory()->count(5)->create();

        $vehicles = Vehicle::factory()->count(10)->create();

        $vehicles->each(function ($vehicle) {

            Stay::factory()
                ->count(rand(1, 3))
                ->create([
                    'vehicle_id' => $vehicle->id,
                    'zone_id' => Zone::inRandomOrder()->first()->id,
                    'entry' => now(),
                ]);
        });
    }
}