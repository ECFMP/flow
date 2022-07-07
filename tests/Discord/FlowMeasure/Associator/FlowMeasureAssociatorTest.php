<?php

namespace Tests\Discord\FlowMeasure\Associator;

use App\Discord\FlowMeasure\Associator\FlowMeasureAssociator;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use Tests\TestCase;

class FlowMeasureAssociatorTest extends TestCase
{
    public function testItAssociatesANotificationWithAFlowMeasure()
    {
        $notification = DiscordNotification::factory()->create();
        $measure = FlowMeasure::factory()->create();

        $associator = new FlowMeasureAssociator($measure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $associator->associate($notification);

        $this->assertDatabaseCount('discord_notification_flow_measure', 1);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'discord_notification_id' => $notification->id,
                'flow_measure_id' => $measure->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure->identifier,
            ]
        );
    }
}
