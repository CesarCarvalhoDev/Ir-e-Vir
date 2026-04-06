<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->bothify('???-####')),
            'type' => $this->faker->randomElement(['CAR', 'MOTORCYCLE']),
            'has_registration' => $this->faker->boolean(),
            'available_balance' => $this->faker->randomFloat(2, 0, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
