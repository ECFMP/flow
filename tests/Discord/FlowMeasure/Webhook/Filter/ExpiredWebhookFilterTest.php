<?php

namespace Tests\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\FlowMeasure\Webhook\Filter\ExpiredWebhookFilter;
use App\Discord\Webhook\EcfmpWebhook;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Tests\TestCase;

class ExpiredWebhookFilterTest extends TestCase
{
    private readonly ExpiredWebhookFilter $filter;
    private readonly EcfmpWebhook $ecfmpWebhook;
    private readonly DivisionDiscordWebhook $divisionDiscordWebhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = $this->app->make(ExpiredWebhookFilter::class);
        $this->ecfmpWebhook = $this->app->make(EcfmpWebhook::class);
        $this->divisionDiscordWebhook = DivisionDiscordWebhook::factory()->create();
    }

    public function testItShouldUseEcfmpWebhookIfLotsOfNotificationsHaveBeenSentRecently()
    {
        $measure = FlowMeasure::factory()->create();
        DivisionDiscordNotification::factory()->count(6)->create(['division_discord_webhook_id' => null]);

        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseEcfmpWebhookIfTheFlowMeasureHasBeenModifiedTwice()
    {
        $measure = FlowMeasure::factory()->create(['identifier' => 'EGTT23A-3']);

        $this->assertTrue(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldUseEcfmpWebhookIfThereAreOtherMeasuresActiveAroundTheTime()
    {
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->subMinutes(30), Carbon::now()->addMinutes(45))
            ->count(3)
            ->create();

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
        $measure = FlowMeasure::factory()->create(['identifier' => 'EGTT23A-3']);
        $discordNotification = DivisionDiscordNotification::factory()->create();
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

    public function testItShouldUseWebhookIfFlowMeasureHasOnlyBeenActivatedToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create(['identifier' => 'EGTT23A-3']);
        $discordNotification = DivisionDiscordNotification::factory()->create();
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

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenWithdrawnToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create(['identifier' => 'EGTT23A-3']);
        $discordNotification = DivisionDiscordNotification::factory()->create();
        $measure->discordNotifications()->attach(
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

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenExpiredToEcfmpWebhook()
    {
        $measure = FlowMeasure::factory()->create(['identifier' => 'EGTT23A-3']);
        $discordNotification = DivisionDiscordNotification::factory()->create();
        $measure->discordNotifications()->attach(
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

    public function testItShouldNotUseWebhookIfDoesntMeetConditionsForEcfmpWebhook()
    {
        // Once revised
        $measure = FlowMeasure::factory()->create(['identifier' => 'EGTT23A-2']);

        // Two other measures
        FlowMeasure::factory()
            ->withTimes($measure->start_time->clone()->subMinutes(2), $measure->start_time->clone()->addMinutes(2))
            ->count(2)
            ->create();

        // Not too many recently sent
        DivisionDiscordNotification::factory()->count(5)->create(['division_discord_webhook_id' => null]);
        DivisionDiscordNotification::factory()->count(5)
            ->toDivisionWebhook(DivisionDiscordWebhook::factory()->create())
            ->create();

        $this->assertFalse(
            $this->filter->shouldUseWebhook(
                $measure,
                $this->ecfmpWebhook
            )
        );
    }

    public function testItShouldNotUseWebhookIfFlowMeasureHasOnlyBeenNotifiedToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()
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

    public function testItShouldNotUseWebhookIfFlowMeasureHasBeenExpiredToDivisionWebhook()
    {
        $measure = FlowMeasure::factory()->create();
        $discordNotification = DivisionDiscordNotification::factory()
            ->toDivisionWebhook($this->divisionDiscordWebhook)
            ->create();
        $measure->discordNotifications()->attach(
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
        $measure->discordNotifications()->attach(
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
