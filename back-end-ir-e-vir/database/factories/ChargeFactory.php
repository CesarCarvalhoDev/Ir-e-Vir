<?php

namespace Database\Factories;

use App\Models\Charge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Charge>
 */
class ChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => $this->faker->randomFloat(2, 5, 500),

            'status' => $this->faker->randomElement([
                'PENDING',
                'PAID',
                'OVERDUE'
            ]),

            'due_date' => $this->faker->dateTimeBetween('now', '+7 days'),
        ];
    }
}
