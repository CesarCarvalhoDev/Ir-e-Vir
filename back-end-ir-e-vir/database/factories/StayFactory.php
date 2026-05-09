<?php

namespace Database\Factories;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Testing\Fakes\Fake;
use Nette\Utils\Random;

/**
 * @extends Factory<Model>
 */
class StayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entry = $this->faker->dateTime();
        $exit = (clone $entry)->modify('+' . rand(1, 120) . ' minutes');

        $totalTime = (int) (($exit->getTimestamp() - $entry->getTimestamp()) / 60);

        return [
            'entry' => $entry,
            'exit' => $exit,
            'total_time' => $totalTime,
            'status' => 'ACTIVE',
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'zone_id' => \App\Models\Zone::inRandomOrder()->first()?->id?? \App\Models\Zone::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
