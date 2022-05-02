<?php

namespace Database\Factories;

use App\Models\FlightInformationRegion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'date_start' => $this->faker->dateTimeBetween('-1 hour'),
            'date_end' => $this->faker->dateTimeBetween('+1 hour', '+2 hour'),
            'flight_information_region_id' => FlightInformationRegion::factory()->create()->id,
            'vatcan_code' => null,
            'participants' => null,
        ];
    }

    public function finished(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_start' => $this->faker->dateTimeBetween('-3 hour', 'now'),
            'date_end' => $this->faker->dateTimeBetween('-2 hour', 'now - 1 minute'),
        ]);
    }

    public function notStarted(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_start' => $this->faker->dateTimeBetween('now + 1 minute', 'now + 1 hour'),
            'date_end' => $this->faker->dateTimeBetween('now + 2 hour', 'now + 3 hour'),
        ]);
    }

    public function withVatcanCode(): static
    {
        return $this->state(fn(array $attributes) => [
            'vatcan_code' => $this->faker->word(),
        ]);
    }

    public function withParticipants(): static
    {
        return $this->state(function () {
            $participants = [];
            for ($i = 0; $i < $this->faker->numberBetween(1, 8); $i++) {
                $participants[] = $this->faker->unique()->numberBetween(900000, 1600000);
            }

            return ['participants' => $participants];
        });
    }
}
