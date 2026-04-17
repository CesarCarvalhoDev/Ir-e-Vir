<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tariff;

class StayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'entry' => function (array $attributes) {
                $tariff = Tariff::where('zone_id', $attributes['zone_id'])->inRandomOrder()->first();

                return $this->faker->dateTimeBetween(
                    $tariff->start_date,
                    $tariff->end_date
                );
            },

            'exit' => function (array $attributes) {
                $tariff = Tariff::where('zone_id', $attributes['zone_id'])->inRandomOrder()->first();

                $entry = $attributes['entry'];

                $exit = (clone $entry)->modify('+' . rand(1, 120) . ' minutes');

                if ($exit > $tariff->end_date) {
                    $exit = (clone $tariff->end_date);
                }

                return $exit;
            },

            'total_time' => function (array $attributes) {
                return (int)(($attributes['exit']->getTimestamp() - $attributes['entry']->getTimestamp()) / 60);
            },

            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}




