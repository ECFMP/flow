<?php

namespace Database\Factories;

use App\Models\DiscordTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscordTag>
 */
class DiscordTagFactory extends Factory
{
    protected $model = DiscordTag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tag' => sprintf('@%s', $this->faker->unique()->word()),
            'description' => $this->faker->sentence(4),
        ];
    }

    public function withoutAtSymbol(): static
    {
        return $this->state(fn(array $attributes) => [
            'tag' => ltrim($attributes['tag'], '@'),
        ]);
    }
}
