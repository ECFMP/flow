<?php

namespace Database\Factories;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscordTag>
 */
class DiscordNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'flow_measure_id' => FlowMeasure::factory()->create()->id,
            'type' => DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
            'content' => 'ohai',
        ];
    }
}
