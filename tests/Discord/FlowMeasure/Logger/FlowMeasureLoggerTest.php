<?php

namespace Tests\Discord\FlowMeasure\Logger;

use App\Discord\FlowMeasure\Logger\FlowMeasureLogger;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotification;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Tests\TestCase;

class FlowMeasureLoggerTest extends TestCase
{
    public function testItLogsTheNotificationForEcfmp()
    {
        $notification = DiscordNotification::factory()->create();
        $measure = FlowMeasure::factory()->create();

        $logger = new FlowMeasureLogger($measure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $logger->log($notification);

        $this->assertDatabaseHas(
            'activity_log',
            [
                'log_name' => 'Discord',
                'description' => 'Sending discord notification',
                'subject_type' => 'App\Models\DiscordNotification',
                'event' => $measure->identifier . ' - Activated',
            ]
        );
    }

    public function testItLogsTheNotificationForDivisions()
    {
        $webhook = DivisionDiscordWebhook::factory()->create();
        $notification = DiscordNotification::factory()->toDivisionWebhook($webhook)->create();
        $measure = FlowMeasure::factory()->create();

        $logger = new FlowMeasureLogger($measure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $logger->log($notification);

        $this->assertDatabaseHas(
            'activity_log',
            [
                'log_name' => 'Discord',
                'description' => 'Sending discord notification',
                'subject_type' => 'App\Models\DiscordNotification',
                'event' => $measure->identifier . ' - Activated',
            ]
        );
    }
}
