<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\NotifiedRepository;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotifiedRepositoryTest extends TestCase
{
    private readonly NotifiedRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotifiedRepository();
    }

    public function testItHasANotificationType()
    {
        $this->assertEquals(
            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
            $this->repository->notificationType()
        );
    }

    public function testItReturnsNotifiedFlowMeasures()
    {
        $measures = FlowMeasure::factory()->notified()->count(3)->create();

        $this->assertEquals(
            $measures->pluck('id')->toArray(),
            $this->repository->flowMeasuresForNotification()->pluck('id')->toArray()
        );
    }

    public function testItIgnoresActiveFlowMeasures()
    {
        FlowMeasure::factory()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresExpiredFlowMeasures()
    {
        FlowMeasure::factory()->finished()->count(3)->create();
        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItIgnoresDeletedFlowMeasures()
    {
        FlowMeasure::factory()->notified()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->delete();
            }
        )->count(3)->create();

        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItReturnsMeasuresToBeSentToEcfmp()
    {
        // Should be sent, never notified
        [$measure1, $measure2] = FlowMeasure::factory()->notified()->count(2)->create();

        // Should be sent, previously notified under a different identifier
        $measure3 = FlowMeasure::factory()->notified()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                        'notified_as' => 'different_identifier',
                    ]
                );
            }
        )
        ->create();

        // Should not be sent, has been previously activated
        FlowMeasure::factory()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                        'notified_as' => $measure->identifier,
                    ]
                );
            }
        );

        // Should not be sent, already notified with this identifier
        FlowMeasure::factory()->afterCreating(
            function (FlowMeasure $measure) {
                $measure->discordNotifications()->create(
                    [
                        'remote_id' => Str::uuid(),
                    ],
                    joining: [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                        'notified_as' => $measure->identifier,
                    ]
                );
            }
        );

        // Should not be sent, already active
        FlowMeasure::factory()->count(3)->create();

        // Should not be sent, not yet in notified range
        FlowMeasure::factory()->notNotified()->count(3)->create();

        // Should not be sent, expired
        FlowMeasure::factory()->finished()->count(3)->create();

        // Should not be sent, withdrawn
        FlowMeasure::factory()->notified()->withdrawn()->count(3)->create();

        $this->assertEquals(
            [$measure1->id, $measure2->id, $measure3->id],
            $this->repository->flowMeasuresToBeSentToEcfmp()->pluck('measure.id')->toArray()
        );
    }

    public function testItIsNotReissueIfNeverPreviouslyNotified()
    {
        // Should send, is active and never notified
        $measure = FlowMeasure::factory()->notified()->create();
        $measuresToNotify = $this->repository->flowMeasuresToBeSentToEcfmp();

        $this->assertCount(1, $measuresToNotify);
        $this->assertEquals($measure->id, $measuresToNotify->first()->measure->id);
        $this->assertFalse($measuresToNotify->first()->isReissuedNotification);
    }

    public function testItIsReissuedIfPreviousWasNotifiedUnderDifferentIdentifier()
    {
        // Should send, is active and never notified
        $measure = FlowMeasure::factory()->notified()->create();
        $measure->discordNotifications()->create(
            [
                'remote_id' => Str::uuid(),
            ],
            joining: [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                'notified_as' => 'something_else',
            ]
        );

        $measuresToNotify = $this->repository->flowMeasuresToBeSentToEcfmp();

        $this->assertCount(1, $measuresToNotify);
        $this->assertEquals($measure->id, $measuresToNotify->first()->measure->id);
        $this->assertTrue($measuresToNotify->first()->isReissuedNotification);
    }
}
