<?php

use App\Models\Charge;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Stay;
use App\Models\Tariff;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Models\Zone;
use App\Services\Charge\GenerateChargeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function freeFlowFixture(float $walletBalance = 120, int $maximumTime = 120): array
{
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'available_balance' => 0,
    ]);

    $user = User::factory()->create([
        'role' => User::ROLE_USER,
        'available_balance' => $walletBalance,
    ]);

    $wallet = Wallet::create([
        'user_id' => $user->id,
        'balance' => $walletBalance,
        'status' => Wallet::STATUS_ACTIVE,
    ]);

    $vehicle = Vehicle::factory()->create([
        'plate' => 'ABC-1234',
        'type' => 'CAR',
        'has_registration' => true,
    ]);

    $user->vehicles()->attach($vehicle->id);

    $zone = Zone::factory()->create([
        'maximum_time' => $maximumTime,
    ]);

    Tariff::factory()->create([
        'zone_id' => $zone->id,
        'hourly_rate' => 60,
        'start_date' => Carbon::parse('2026-01-01 00:00:00'),
        'end_date' => Carbon::parse('2026-12-31 23:59:59'),
        'active' => true,
    ]);

    return compact('admin', 'user', 'wallet', 'vehicle', 'zone');
}

it('registra entrada criando permanência ativa', function () {
    ['admin' => $admin, 'vehicle' => $vehicle, 'zone' => $zone] = freeFlowFixture();

    Sanctum::actingAs($admin);

    $this->postJson('/api/admin/stays/entry', [
        'entry' => '2026-06-05 08:00:00',
        'plate' => $vehicle->plate,
        'zone_id' => $zone->id,
    ])
        ->assertCreated()
        ->assertJsonPath('status', Stay::STATUS_ACTIVE);

    $this->assertDatabaseHas('stays', [
        'vehicle_id' => $vehicle->id,
        'zone_id' => $zone->id,
        'status' => Stay::STATUS_ACTIVE,
    ]);
});

it('registra saída com saldo criando cobrança, pagamento e finalizando permanência', function () {
    ['admin' => $admin, 'wallet' => $wallet, 'vehicle' => $vehicle, 'zone' => $zone] = freeFlowFixture(walletBalance: 120);

    Stay::create([
        'entry' => '2026-06-05 08:00:00',
        'vehicle_id' => $vehicle->id,
        'zone_id' => $zone->id,
        'status' => Stay::STATUS_ACTIVE,
    ]);

    Sanctum::actingAs($admin);

    $this->postJson('/api/admin/stays/exit', [
        'exit' => '2026-06-05 09:00:00',
        'plate' => $vehicle->plate,
    ])
        ->assertOk()
        ->assertJsonPath('amount_paid', 60);

    $stay = Stay::first();
    $charge = Charge::first();

    expect($stay->fresh()->status)->toBe(Stay::STATUS_FINISHED)
        ->and((int) $stay->fresh()->total_time)->toBe(60)
        ->and($charge->status)->toBe(Charge::STATUS_PAID)
        ->and(Payment::where('charges_id', $charge->id)->count())->toBe(1)
        ->and((float) $wallet->fresh()->balance)->toBe(60.0);
});

it('registra saída sem saldo mantendo cobrança pendente e gerando multa por saldo insuficiente', function () {
    ['admin' => $admin, 'vehicle' => $vehicle, 'zone' => $zone] = freeFlowFixture(walletBalance: 10);

    Stay::create([
        'entry' => '2026-06-05 08:00:00',
        'vehicle_id' => $vehicle->id,
        'zone_id' => $zone->id,
        'status' => Stay::STATUS_ACTIVE,
    ]);

    Sanctum::actingAs($admin);

    $this->postJson('/api/admin/stays/exit', [
        'exit' => '2026-06-05 09:00:00',
        'plate' => $vehicle->plate,
    ])->assertStatus(400);

    expect(Stay::first()->status)->toBe(Stay::STATUS_IRREGULAR)
        ->and(Charge::first()->status)->toBe(Charge::STATUS_PENDING)
        ->and(Fine::where('reason', Fine::REASON_INSUFFICIENT_BALANCE)->count())->toBe(1);
});

it('gera multa quando a permanência excede o tempo máximo da zona', function () {
    ['admin' => $admin, 'vehicle' => $vehicle, 'zone' => $zone] = freeFlowFixture(walletBalance: 120, maximumTime: 30);

    Stay::create([
        'entry' => '2026-06-05 08:00:00',
        'vehicle_id' => $vehicle->id,
        'zone_id' => $zone->id,
        'status' => Stay::STATUS_ACTIVE,
    ]);

    Sanctum::actingAs($admin);

    $this->postJson('/api/admin/stays/exit', [
        'exit' => '2026-06-05 09:00:00',
        'plate' => $vehicle->plate,
    ])->assertOk();

    expect(Stay::first()->status)->toBe(Stay::STATUS_FINISHED)
        ->and(Fine::where('reason', Fine::REASON_TIME_LIMIT_EXCEEDED)->count())->toBe(1);
});

it('usuário lista somente suas cobranças e multas', function () {
    ['user' => $user, 'vehicle' => $vehicle, 'zone' => $zone] = freeFlowFixture();

    $stay = Stay::create([
        'entry' => '2026-06-05 08:00:00',
        'exit' => '2026-06-05 09:00:00',
        'total_time' => 60,
        'vehicle_id' => $vehicle->id,
        'zone_id' => $zone->id,
        'status' => Stay::STATUS_FINISHED,
    ]);

    $charge = Charge::create([
        'value' => 60,
        'status' => Charge::STATUS_PENDING,
        'due_date' => now()->addMinutes(15),
        'stay_id' => $stay->id,
        'user_id' => $user->id,
    ]);

    Fine::create([
        'user_id' => $user->id,
        'stay_id' => $stay->id,
        'amount' => 60,
        'reason' => Fine::REASON_TIME_LIMIT_EXCEEDED,
        'status' => Fine::STATUS_ACTIVE,
        'started_at' => now(),
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/me/charges')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $charge->id);

    $this->getJson('/api/me/fines')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.reason', Fine::REASON_TIME_LIMIT_EXCEEDED);
});

it('não duplica cobrança para a mesma permanência', function () {
    ['user' => $user, 'vehicle' => $vehicle, 'zone' => $zone] = freeFlowFixture();

    $stay = Stay::create([
        'entry' => '2026-06-05 08:00:00',
        'exit' => '2026-06-05 09:00:00',
        'total_time' => 60,
        'vehicle_id' => $vehicle->id,
        'zone_id' => $zone->id,
        'status' => Stay::STATUS_FINISHED,
    ]);

    $service = app(GenerateChargeService::class);

    $first = $service->execute($stay, 60, $user);
    $second = $service->execute($stay, 60, $user);

    expect($second->id)->toBe($first->id)
        ->and(Charge::where('stay_id', $stay->id)->count())->toBe(1);
});
