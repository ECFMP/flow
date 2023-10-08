<?php

namespace Database\Factories;

use App\Models\DivisionDiscordWebhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscordTag>
 */
class DivisionDiscordNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'division_discord_webhook_id' => null,
            'content' => 'ohai',
            'embeds' => [
                'foo' => 'var',
            ],
        ];
    }

    public function toDivisionWebhook(DivisionDiscordWebhook $divisionDiscordWebhook): static
    {
        return $this->state(fn (array $attributes) => [
            'division_discord_webhook_id' => $divisionDiscordWebhook->id,
        ]);
    }
}
