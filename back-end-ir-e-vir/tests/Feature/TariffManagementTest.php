<?php

use App\Models\Tariff;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('permite que administrador crie e liste tarifas', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $zone = Zone::factory()->create();

    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/admin/tariffs', [
        'hourly_rate' => 12.50,
        'start_date' => '2026-06-11 00:00:00',
        'end_date' => '2026-12-31 23:59:59',
        'active' => true,
        'zone_id' => $zone->id,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('hourly_rate', 12.5)
        ->assertJsonPath('zone_id', $zone->id)
        ->assertJsonPath('active', true);

    $this->assertDatabaseHas('tariffs', [
        'zone_id' => $zone->id,
        'hourly_rate' => 12.50,
        'active' => true,
    ]);

    $this->getJson('/api/admin/tariffs')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', Tariff::first()->id);
});

it('bloqueia criação de tarifa para usuário comum', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);
    $zone = Zone::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/admin/tariffs', [
        'hourly_rate' => 10,
        'start_date' => '2026-06-11 00:00:00',
        'end_date' => '2026-12-31 23:59:59',
        'active' => true,
        'zone_id' => $zone->id,
    ])->assertForbidden();
});
