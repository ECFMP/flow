<?php

namespace Tests\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\FlowMeasure\Webhook\Filter\WithdrawnWebhookFilter;
use App\Discord\Webhook\EcfmpWebhook;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Tests\TestCase;

class WithdrawnWebhookFilterTest extends TestCase
{
    private readonly WithdrawnWebhookFilter $filter;
    private readonly EcfmpWebhook $ecfmpWebhook;
    private readonly DivisionDiscordWebhook $divisionDiscordWebhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = $this->app->make(WithdrawnWebhookFilter::class);
        $this->ecfmpWebhook = $this->app->make(EcfmpWebhook::class);
        $this->divisionDiscordWebhook = DivisionDiscordWebhook::factory()->create();
    }

    public function testItShouldUseEcfmpWebhookIfItHasBeenNotified()
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
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseEcfmpWebhookIfItHasBeenActivated()
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
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldNotUseEcfmpWebhookIfItHasBeenWithdrawn()
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
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldNotUseEcfmpWebhookIfItHasBeenExpired()
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
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseDivisionWebhookIfItHasBeenNotified()
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
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldUseDivisionWebhookIfItHasBeenActivated()
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
        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->divisionDiscordWebhook
            )
        );
    }

    public function testItShouldNotUseDivisionWebhookIfItHasBeenWithdrawn()
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

    public function testItShouldNotUseDivisionWebhookIfItHasBeenExpired()
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
}
