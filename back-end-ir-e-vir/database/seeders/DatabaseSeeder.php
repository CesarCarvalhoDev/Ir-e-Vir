<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Vehicle;
use App\Models\Stay;
use App\Models\Charge;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

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

        $admin = User::factory()->create([
            'name' => 'Administrador Ir e Vir',
            'email' => 'admin@irevir.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'available_balance' => 0,
        ]);

        $user = User::factory()->create([
            'name' => 'Usuário Ir e Vir',
            'email' => 'user@irevir.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'available_balance' => 250,
        ]);

        $users = User::factory()->count(4)->create();

        $users->push($admin, $user)->each(function (User $seededUser) {
            Wallet::firstOrCreate(
                ['user_id' => $seededUser->id],
                ['balance' => $seededUser->available_balance, 'status' => Wallet::STATUS_ACTIVE]
            );
        });

        $vehicles = Vehicle::factory()->count(10)->create();

        $user->vehicles()->syncWithoutDetaching(
            $vehicles->take(2)->pluck('id')->all()
        );

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
