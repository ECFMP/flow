<?php

namespace Tests\Discord\FlowMeasure\Helper;

use App\Discord\FlowMeasure\Helper\NotificationReissuer;
use App\Discord\Webhook\EcfmpWebhook;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->type()
        );
    }

    public function testItHasAFlowMeasure()
    {
        $this->assertEquals(
            $this->flowMeasure,
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->measure()
        );
    }

    public function testItsAReissueIfItsNotifiedAndTheIdentifierHasChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsActivatedAndTheIdentifierHasChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItWasNotifiedAndTheIdentifierHasChangedForActivation()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsNotifiedOnAEcfmpWebhook()
    {
        $previousNotificationEcfmp = DivisionDiscordNotification::factory()
            ->create();

        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $previousDivisionNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($divisionWebhook)
            ->create();

        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotificationEcfmp->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'notthis',
                ],
                $previousDivisionNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
            ]
        );

        $this->assertTrue(
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsNotifiedOnADifferentDivisionWebhook()
    {
        $otherDivisionWebhook = DivisionDiscordWebhook::factory()->create();
        $previousNotificationOtherDivision = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($otherDivisionWebhook)
            ->create();

        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $previousDivisionNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($divisionWebhook)
            ->create();

        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotificationOtherDivision->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
                $previousDivisionNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'nothis',
                ],
            ]
        );

        $this->assertTrue(
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    $divisionWebhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsActivatedOnADifferentDivisionWebhook()
    {
        $otherDivisionWebhook = DivisionDiscordWebhook::factory()->create();
        $previousNotificationOtherDivision = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($otherDivisionWebhook)
            ->create();

        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $previousDivisionNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($divisionWebhook)
            ->create();

        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotificationOtherDivision->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
                $previousDivisionNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => 'notthis',
                ],
            ]
        );

        $this->assertTrue(
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    $divisionWebhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsActivatedOnAEcfmpWebhook()
    {
        $previousNotification = DivisionDiscordNotification::factory()
            ->create();

        $this->flowMeasure->discordNotifications()->sync(
            [
                $previousNotification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => 'nothis',
                ],
            ]
        );

        $this->assertTrue(
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndTheIdentifierHasNotChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsActivatedAndTheIdentifierHasNotChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndThenActivatedTheIdentifierHasNotChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndNeverBeenNotified()
    {
        $this->assertFalse(
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndNeverBeenActivated()
    {
        $this->assertFalse(
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsWithdrawn()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsExpired()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
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
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED,
                    new EcfmpWebhook()
                )
            )->isReissuedNotification()
        );
    }
}
