<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Zone;

class TariffFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-30 days', '-1 days');
        $end = (clone $start)->modify('+30 days');

        return [
            'hourly_rate' => $this->faker->randomFloat(2, 5, 20),
            'start_date' => $start,
            'end_date' => $end,
            'active' => true,
            'zone_id' => Zone::factory(),
        ];
    }
}
