<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\WithdrawnRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class WithdrawnRepositoryTest extends TestCase
{
    private readonly WithdrawnRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new WithdrawnRepository();
    }

    public function testItHasANotificationType()
    {
        $this->assertEquals(
            DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN,
            $this->repository->notificationType()
        );
    }

    public function testItReturnsWithdrawnWouldBeActiveFlowMeasures()
    {
        $measures = FlowMeasure::factory()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->delete();
            })
            ->count(3)
            ->create();

        $this->assertEquals(
            $measures->pluck('id')->toArray(),
            $this->repository->flowMeasuresForNotification()->pluck('id')->toArray()
        );
    }

    public function testItReturnsWithdrawnWouldBeNotifiedFlowMeasures()
    {
        $measures = FlowMeasure::factory()
            ->notified()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->delete();
            })
            ->count(3)
            ->create();

        $this->assertEquals(
            $measures->pluck('id')->toArray(),
            $this->repository->flowMeasuresForNotification()->pluck('id')->toArray()
        );
    }

    public function testItIgnoresNotifiedFlowMeasuresThatWereDeletedALongTimeAgo()
    {
        FlowMeasure::factory()
            ->notified()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->deleted_at = Carbon::now()->subHour()->subMinute();
                $measure->save();
            })
            ->count(3)
            ->create();

        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresActiveFlowMeasuresThatWereDeletedALongTimeAgo()
    {
        FlowMeasure::factory()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->deleted_at = Carbon::now()->subHour()->subMinute();
                $measure->save();
            })
            ->count(3)
            ->create();

        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresNonDeletedActiveFlowMeasures()
    {
        FlowMeasure::factory()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresNonDeletedNotifiedFlowMeasures()
    {
        FlowMeasure::factory()->notified()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresNonDeletedExpiredFlowMeasures()
    {
        FlowMeasure::factory()->finished()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresDeletedExpiredFlowMeasures()
    {
        FlowMeasure::factory()
            ->finished()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->delete();
            })
            ->count(3)
            ->create();

        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItReturnsFlowMeasuresToSendToEcfmp()
    {
        // Should be sent, was notified and is now withdrawn
        $measure1 = FlowMeasure::factory()
            ->withdrawn()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                        ),
                        'notified_as' => $measure->identifier
                    ]
                );
            })
            ->create();

        // Should be sent, was active and is now withdrawn
        $measure2 = FlowMeasure::factory()
            ->withdrawn()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                        ),
                        'notified_as' => $measure->identifier
                    ]
                );
            })
            ->create();

        // Should not be sent, is active
        FlowMeasure::factory()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                        ),
                        'notified_as' => $measure->identifier
                    ]
                );
            })
            ->create();

        // Should not be sent, is notified
        FlowMeasure::factory()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                        ),
                        'notified_as' => $measure->identifier
                    ]
                );
            })
            ->notified()
            ->create();

        // Should not be sent, is widthdrawn but already notified as withdrawn with some identifier
        FlowMeasure::factory()
            ->withdrawn()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                        ),
                        'notified_as' => 'an_identifier'
                    ]
                );
            })
            ->create();

        // Should not be sent, is widthdrawn but already notified as expired with some identifier
        FlowMeasure::factory()
            ->withdrawn()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED
                        ),
                        'notified_as' => 'an_identifier'
                    ]
                );
            })
            ->create();
        // DB::commit();
        // dd('hi');

        $this->assertEquals(
            [$measure1->id, $measure2->id],
            $this->repository->flowMeasuresToBeSentToEcfmp()->pluck('measure.id')->toArray()
        );
    }

    public function testItIsNotReissued()
    {
        // Should be sent, was notified and is now withdrawn
        $measure = FlowMeasure::factory()
            ->withdrawn()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                        ),
                        'notified_as' => $measure->identifier
                    ]
                );
            })
            ->create();

        $this->assertEquals($measure->id, $this->repository->flowMeasuresToBeSentToEcfmp()->first()->measure->id);
        $this->assertFalse($this->repository->flowMeasuresToBeSentToEcfmp()->first()->isReissuedNotification);
    }
}
