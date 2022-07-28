<?php

namespace Database\Factories;

use App\Models\EventParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class EventParticipantFactory extends Factory
{
    protected $model = EventParticipant::class;

    public function definition(): array
    {
        return [
            'cid' => $this->faker->unique()->numberBetween(900000, 1600000),
            'origin' => Str::upper($this->faker->unique()->lexify()),
            'destination' => Str::upper($this->faker->unique()->lexify()),
        ];
    }
}
