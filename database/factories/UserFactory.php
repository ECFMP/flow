<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => $this->faker->unique()->numberBetween(900000, 1600000),
            'name' => $this->faker->name(),
            'role_id' => Role::where('key', 'USER')->firstOrFail()->id,
        ];
    }

    public function flowManager()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('key', 'FLOW_MANAGER')->firstOrFail()->id,
            ];
        });
    }

    public function networkManager()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('key', 'NMT')->firstOrFail()->id,
            ];
        });
    }

    public function system()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('key', 'SYSTEM')->firstOrFail()->id,
            ];
        });
    }
}
