<?php

namespace Database\Factories;

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
        ];
    }

}
