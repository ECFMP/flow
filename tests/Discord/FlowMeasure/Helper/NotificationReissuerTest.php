<?php

namespace Tests\Discord\FlowMeasure\Helper;

use App\Discord\FlowMeasure\Helper\NotificationReissuer;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Tests\TestCase;

class NotificationReissuerTest extends TestCase
{
    private readonly FlowMeasure $flowMeasure;
    private readonly DivisionDiscordWebhook $webhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->flowMeasure = FlowMeasure::factory()->create();
        $this->webhook = DivisionDiscordWebhook::factory()
            ->create();
    }

    public function testItHasAType()
    {
        $this->assertEquals(
            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
            (
                new NotificationReissuer(
                    $this->flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    $this->webhook
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
                    $this->webhook
                )
            )->measure()
        );
    }

    public function testItsAReissueIfItsNotifiedAndTheIdentifierHasChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItsActivatedAndTheIdentifierHasChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItsAReissueIfItWasNotifiedAndTheIdentifierHasChangedForActivation()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
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

        $previousDivisionNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->webhook)
            ->create();

        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
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

        $previousDivisionNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->webhook)
            ->create();

        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndTheIdentifierHasNotChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsActivatedAndTheIdentifierHasNotChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsNotifiedAndThenActivatedTheIdentifierHasNotChanged()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
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
                    $this->webhook
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsWithdrawn()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }

    public function testItIsNotAReissueIfItsExpired()
    {
        $previousNotification = DivisionDiscordNotification::factory()->create();
        $this->flowMeasure->divisionDiscordNotifications()->sync(
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
                    $this->webhook
                )
            )->isReissuedNotification()
        );
    }
}
