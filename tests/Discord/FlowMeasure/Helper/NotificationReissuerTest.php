<?php

namespace Tests\Discord\FlowMeasure\Helper;

use App\Discord\FlowMeasure\Helper\NotificationReissuer;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use Tests\TestCase;

class NotificationReissuerTest extends TestCase
{
    private readonly FlowMeasure $flowMeasure;

    public function setUp(): void
    {
        parent::setUp();
        $this->flowMeasure = FlowMeasure::factory()->create();
    }

    public function testItHasAType()
    {
        $this->assertEquals(
            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
            ))->type()
        );
    }

    public function testItHasAFlowMeasure()
    {
        $this->assertEquals(
            $this->flowMeasure,
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
            ))->measure()
        );
    }

    public function testItsAReissueIfItsNotifiedAndTheIdentifierHasChanged()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'notthis',
                ],
            ]
        );

        $this->assertTrue(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
            ))->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsActivatedAndTheIdentifierHasChanged()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => 'notthis',
                ],
            ]
        );

        $this->assertTrue(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
            ))->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItWasNotifiedAndTheIdentifierHasChangedForActivation()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'notthis',
                ],
            ]
        );

        $this->assertTrue(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
            ))->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndTheIdentifierHasNotChanged()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
            ))->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsActivatedAndTheIdentifierHasNotChanged()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
            ))->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndThenActivatedTheIdentifierHasNotChanged()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
            ]
        );

        $this->assertFalse(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
            ))->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsWithdrawn()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'notthis',
                ],
            ]
        );

        $this->assertFalse(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
            ))->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsExpired()
    {
        $previousNotification = DiscordNotification::factory()->create();
        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'notthis',
                ],
            ]
        );

        $this->assertFalse(
            (new NotificationReissuer(
                $this->flowMeasure, DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED
            ))->isReissuedNotification()
        );
    }
}
