<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\ActiveRepository;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActiveRepositoryTest extends TestCase
{
    private readonly ActiveRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ActiveRepository();
    }

    public function testItHasANotificationType()
    {
        $this->assertEquals(
            DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
            $this->repository->notificationType()
        );
    }

    public function testItReturnsActiveFlowMeasures()
    {
        $measures = FlowMeasure::factory()->count(3)->create();

        $this->assertEquals(
            $measures->pluck('id')->toArray(),
            $this->repository->flowMeasuresForNotification()->pluck('id')->toArray()
        );
    }

    public function testItIgnoresDeletedFlowMeasures()
    {
        FlowMeasure::factory()->afterCreating(
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

    public function testItIgnoresExpiredFlowMeasures()
    {
        FlowMeasure::factory()->finished()->count(3)->create();

        $this->assertEmpty($this->repository->flowMeasuresForNotification());
    }

    public function testItReturnsEcfmpMeasuresToSend()
    {
        // Should send, is active and never notified
        [$measure1, $measure2] = FlowMeasure::factory()->count(2)->create();

        // Active, but should send, has been notified with a different identifier
        $measure3 = FlowMeasure::factory()
            ->afterCreating(
                function (FlowMeasure $measure) {
                    $measure->discordNotifications()->create(
                        [
                            'remote_id' => Str::uuid(),
                        ],
                        joining: [
                            'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                            'notified_as' => 'different_identifier',
                        ]
                    );
                }
            )
            ->create();

        // Active, but should send, has been notified as a notified type
        $measure4 = FlowMeasure::factory()
            ->afterCreating(
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

        // Should not send, is only notified
        FlowMeasure::factory()->notStarted()->count(1)->create();

        // Should not send, is expired
        FlowMeasure::factory()->finished()->count(1)->create();

        // Should not send, is withdrawn
        FlowMeasure::factory()->withdrawn()->count(1)->create();

        // Active, but should not send, has already been notified with this identifier
        FlowMeasure::factory()
            ->afterCreating(
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
            )
            ->create();

        $this->assertEquals(
            [$measure1->id, $measure2->id, $measure3->id, $measure4->id],
            $this->repository->flowMeasuresToBeSentToEcfmp()->pluck('measure.id')->toArray()
        );
    }

    public function testItIsNotReissueIfNeverPreviouslyActivated()
    {
        // Should send, is active and never notified
        $measure = FlowMeasure::factory()->create();
        $measuresToNotify = $this->repository->flowMeasuresToBeSentToEcfmp();

        $this->assertCount(1, $measuresToNotify);
        $this->assertEquals($measure->id, $measuresToNotify->first()->measure->id);
        $this->assertFalse($measuresToNotify->first()->isReissuedNotification);
    }

    public function testItIsNotReissueIfPreviousVersionWasNotifiedForTheSameIdentifier()
    {
        // Should send, is active and never notified
        $measure = FlowMeasure::factory()->create();
        $measure->discordNotifications()->create(
            [
                'remote_id' => Str::uuid(),
            ],
            joining: [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED),
                'notified_as' => $measure->identifier,
            ]
        );

        $measuresToNotify = $this->repository->flowMeasuresToBeSentToEcfmp();

        $this->assertCount(1, $measuresToNotify);
        $this->assertEquals($measure->id, $measuresToNotify->first()->measure->id);
        $this->assertFalse($measuresToNotify->first()->isReissuedNotification);
    }

    public function testItIsReissuedIfPreviousWasNotifiedUnderDifferentIdentifier()
    {
        // Should send, is active and never notified
        $measure = FlowMeasure::factory()->create();
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

    public function testItIsReissuedIfPreviousWasActivatedUnderDifferentIdentifier()
    {
        // Should send, is active and never notified
        $measure = FlowMeasure::factory()->create();
        $measure->discordNotifications()->create(
            [
                'remote_id' => Str::uuid(),
            ],
            joining: [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED),
                'notified_as' => 'something_else',
            ]
        );

        $measuresToNotify = $this->repository->flowMeasuresToBeSentToEcfmp();

        $this->assertCount(1, $measuresToNotify);
        $this->assertEquals($measure->id, $measuresToNotify->first()->measure->id);
        $this->assertTrue($measuresToNotify->first()->isReissuedNotification);
    }
}
