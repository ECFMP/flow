<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\ExpiredRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExpiredRepositoryTest extends TestCase
{
    private readonly ExpiredRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ExpiredRepository::class);
    }

    public function testItHasANotificationType()
    {
        $this->assertEquals(
            DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED,
            $this->repository->notificationType()
        );
    }

    public function testItReturnsRecentlyExpiredFlowMeasures()
    {
        $measures = FlowMeasure::factory()->finishedRecently()->count(3)->create();

        $this->assertEquals(
            $measures->pluck('id')->toArray(),
            $this->repository->flowMeasuresForNotification()->pluck('id')->toArray()
        );
    }

    public function testItIgnoresFlowMeasuresThatFinishedAWhileAgo()
    {
        FlowMeasure::factory()->finishedAWhileAgo()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresDeletedFlowMeasures()
    {
        FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->delete();
            }
        )->count(3)->create();

        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresNotifiedFlowMeasures()
    {
        FlowMeasure::factory()->notStarted()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresActiveFlowMeasures()
    {
        FlowMeasure::factory()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItReturnsEmptyForEcfmpMeasuresIfMoreThanFiveNotificationsSentInLastTwoHours()
    {
        DiscordNotification::factory()->count(6)->create(['created_at' => Carbon::now()->subHours(2)->addMinute()]);

        // Would be sent to ECFMP if not for the webhook limit
        FlowMeasure::factory()->finishedRecently()->count(3)->create();

        $this->assertEmpty($this->repository->flowMeasuresToBeSentToEcfmp());
    }

    public function testItReturnsEcfmpMeasures()
    {
        // Will be sent, notified as notified previously
        $flowMeasure1 = FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $notification = $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                        'notified_as' => $measure->identifier,
                    ]
                );
                $notification->created_at = Carbon::now()->subHours(3);
                $notification->save();
            }
        )->create();

        // Will be sent, notified as activated previously
        $flowMeasure2 = FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $notification = $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                        'notified_as' => $measure->identifier,
                    ]
                );
                $notification->created_at = Carbon::now()->subHours(3);
                $notification->save();
            }
        )->create();

        // Wont be sent, high revision number
        FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->revision_number = 2;
                $measure->save();
            }
        )->create();

        // Wont be sent, never previously notified
        FlowMeasure::factory()->finishedRecently()->create();

        // Wont be sent, notified as expired previously
        FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $notification1 = $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                        'notified_as' => $measure->identifier,
                    ]
                );
                $notification1->created_at = Carbon::now()->subHours(3);
                $notification1->save();

                $notification2 = $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED),
                        'notified_as' => $measure->identifier,
                    ]
                );
                $notification2->created_at = Carbon::now()->subHours(3);
                $notification2->save();
            }
        )->create();

        // Won't be sent, notified as withdrawn previously
        FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $notification1 = $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                        'notified_as' => $measure->identifier,
                    ]
                );
                $notification1->created_at = Carbon::now()->subHours(3);
                $notification1->save();

                $notification2 = $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN),
                        'notified_as' => $measure->identifier,
                    ]
                );

                $notification2->created_at = Carbon::now()->subHours(3);
                $notification2->save();
            }
        )->create();

        // Won't be sent, notification sent as notified but still notified
        FlowMeasure::factory()->notified()->afterCreating(
            function (FlowMeasure $measure) {
                $notification = $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                        'notified_as' => $measure->identifier,
                    ]
                );

                $notification->created_at = Carbon::now()->subHours(3);
                $notification->save();
            }
        )->create();

        // Won't be sent, notification sent as active but still active
        FlowMeasure::factory()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                        'notified_as' => $measure->identifier,
                    ]
                );
            }
        )->create();

        // Won't be sent, notified as active but expired a long time ago
        FlowMeasure::factory()->finishedAWhileAgo()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'created_at' => Carbon::now()->subHours(3),
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                        'notified_as' => $measure->identifier,
                    ]
                );
            }
        )->create();

        $this->assertEquals(
            [$flowMeasure1->id, $flowMeasure2->id],
            $this->repository->flowMeasuresToBeSentToEcfmp()->pluck('measure.id')->toArray()
        );
    }

    public function testItIsNotReissued()
    {
        // Will be sent, notified as notified previously
        $measure = FlowMeasure::factory()->finishedRecently()->afterCreating(
            function (FlowMeasure $measure) {
                $notification = $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                        'notified_as' => $measure->identifier,
                    ]
                );
                $notification->created_at = Carbon::now()->subHours(3);
                $notification->save();
            }
        )->create();

        $this->assertEquals($measure->id, $this->repository->flowMeasuresToBeSentToEcfmp()->first()->measure->id);
        $this->assertFalse($this->repository->flowMeasuresToBeSentToEcfmp()->first()->isReissuedNotification);
    }
}
