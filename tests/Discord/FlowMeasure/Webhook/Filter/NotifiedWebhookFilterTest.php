<?php

namespace Tests\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\FlowMeasure\Webhook\Filter\NotifiedWebhookFilter;
use App\Discord\Webhook\EcfmpWebhook;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Tests\TestCase;

class NotifiedWebhookFilterTest extends TestCase
{
    private readonly NotifiedWebhookFilter $filter;
    private readonly EcfmpWebhook $ecfmpWebhook;
    private readonly DivisionDiscordWebhook $divisionDiscordWebhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = $this->app->make(NotifiedWebhookFilter::class);
        $this->ecfmpWebhook = $this->app->make(EcfmpWebhook::class);
        $this->divisionDiscordWebhook = DivisionDiscordWebhook::factory()->create();
    }

    public function testItShouldUseWebhookIfFlowMeasureHasNeverBeenActivatedOrNotifiedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseWebhookIfFlowMeasureHasBeenNotifiedToEcfmpWebhookUnderDifferentIdentifier()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()->create();
        $measure->divisionDiscordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'notme',
                ],
            ]
        );

        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenNotifiedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()->create();
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
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenActivatedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()->create();
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
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseWebhookIfFlowMeasureHasNeverBeenActivatedOrNotifiedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldUseWebhookIfFlowMeasureHasOnlyBeenNotifiedToDivisionWebhookAsDifferentIdentifier()
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
                    'notified_as' => 'abc',
                ],
            ]
        );

        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenNotifiedToDivisionWebhookWithSameIdentifier()
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

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenActivatedToDivisionWebhook()
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
}
