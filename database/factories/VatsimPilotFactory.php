<?php

namespace Database\Factories;

use App\Models\VatsimPilotStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VatsimPilotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cid' => $this->faker->unique()->numberBetween(900000, 1600000),
            'callsign' => $this->faker->unique()->word(),
            'altitude' => 123,
            'vatsim_pilot_status_id' => VatsimPilotStatus::Cruise,
            'estimated_arrival_time' => Carbon::now()->addMinutes(30),
            'distance_to_destination' => $this->faker->unique()
                ->randomFloat(
                min: 500,
                max: 1500
                )
        ];
    }

}
