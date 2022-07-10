<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\ActiveRepository;
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
            DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
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
}
