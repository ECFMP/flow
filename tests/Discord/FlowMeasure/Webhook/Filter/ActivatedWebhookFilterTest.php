<?php

namespace Tests\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\FlowMeasure\Webhook\Filter\ActivatedWebhookFilter;
use App\Discord\Webhook\EcfmpWebhook;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Tests\TestCase;

class ActivatedWebhookFilterTest extends TestCase
{
    private readonly ActivatedWebhookFilter $filter;
    private readonly EcfmpWebhook $ecfmpWebhook;
    private readonly DivisionDiscordWebhook $divisionDiscordWebhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = $this->app->make(ActivatedWebhookFilter::class);
        $this->ecfmpWebhook = $this->app->make(EcfmpWebhook::class);
        $this->divisionDiscordWebhook = DivisionDiscordWebhook::factory()->create();
    }

    public function testItShouldUseWebhookIfFlowMeasureHasNeverBeenActivatedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseWebhookIfFlowMeasureHasOnlyBeenNotifiedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DiscordNotification::factory()->create();
        $measure->discordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $measure->identifier,
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

    public function testItShouldUseWebhookIfFlowMeasureHasBeenActivatedAsDifferentIdentifierToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DiscordNotification::factory()->create();
        $measure->discordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => 'NOTHIS',
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

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenActivatedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DiscordNotification::factory()->create();
        $measure->discordNotifications()->attach(
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

    public function testItShouldUseWebhookIfFlowMeasureHasNeverBeenActivatedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldUseWebhookIfFlowMeasureHasOnlyBeenNotifiedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->discordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $measure->identifier,
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

    public function testItShouldUseWebhookIfFlowMeasureHasBeenActivatedAsDifferentIdentifierToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->discordNotifications()->attach(
            [
                $discordNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => 'NOTHIS',
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

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenActivatedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->discordNotifications()->attach(
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
