<?php

namespace Database\Factories;

use App\Models\DivisionDiscordWebhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DivisionDiscordWebhook>
 */
class DivisionDiscordWebhookFactory extends Factory
{
    protected $model = DivisionDiscordWebhook::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url(),
            'description' => $this->faker->sentence(3),
            'tag' => sprintf('@%s', $this->faker->unique()->numberBetween(0, PHP_INT_MAX)),
        ];
    }

    public function withNoTag(): static
    {
        return $this->state(fn (array $attributes) => ['tag' => '']);
    }
}
