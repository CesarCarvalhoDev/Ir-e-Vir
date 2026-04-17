<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Stay;
use App\Models\Charge;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@irever.com',
            'password' => 'password123',  // Laravel vai hashear automaticamente
            'role' => 'admin',
        ]);

        // Create regular users
        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $users[] = User::create([
                'name' => "Usuário {$i}",
                'email' => "usuario{$i}@example.com",
                'password' => 'password123',  // Laravel vai hashear automaticamente
                'role' => 'user',
            ]);
        }

        $zones = Zone::factory()->count(5)->create();

        $zones->each(function ($zone) {
            \App\Models\Tariff::factory()->create([
                'zone_id' => $zone->id,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(10),
                'active' => true,
            ]);
        });

        // Create vehicles for users
        foreach ($users as $user) {
            Vehicle::factory()
                ->count(2)
                ->create(['user_id' => $user->id])
                ->each(function ($vehicle) use ($zones) {
                    Stay::factory()
                        ->count(rand(1, 3))
                        ->create([
                            'vehicle_id' => $vehicle->id,
                            'zone_id' => $zones->random()->id,
                        ])
                        ->each(function ($stay) {
                            Charge::factory()
                                ->count(rand(1, 2))
                                ->create([
                                    'stay_id' => $stay->id,
                                ]);
                        });
                });
        }
    }
}
