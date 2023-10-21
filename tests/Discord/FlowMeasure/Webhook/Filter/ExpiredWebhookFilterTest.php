<?php

namespace Tests\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\FlowMeasure\Webhook\Filter\ExpiredWebhookFilter;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Tests\TestCase;

class ExpiredWebhookFilterTest extends TestCase
{
    private readonly ExpiredWebhookFilter $filter;
    private readonly DivisionDiscordWebhook $divisionDiscordWebhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = $this->app->make(ExpiredWebhookFilter::class);
        $this->divisionDiscordWebhook = DivisionDiscordWebhook::factory()->create();
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasOnlyBeenNotifiedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->divisionDiscordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $measure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasOnlyBeenActivatedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->divisionDiscordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $measure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenExpiredToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->divisionDiscordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED
                    ),
                    'notified_as' => $measure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenWithdrawnToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->divisionDiscordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                    ),
                    'notified_as' => $measure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }
}
