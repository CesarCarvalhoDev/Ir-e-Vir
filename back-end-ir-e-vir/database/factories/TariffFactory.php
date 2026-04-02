<?php

namespace Database\Factories;

use App\Models\Tariff;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tariff>
 */
class TariffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 month');

        return [
            'hourly_rate' => $this->faker->randomFloat(2, 1, 50),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active' => $this->faker->boolean(100),
            'zone_id' => Zone::factory(),
        ];
    }
}
