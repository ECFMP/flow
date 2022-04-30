<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AirportGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->sentence(2) . ' Group',
        ];
    }
}
