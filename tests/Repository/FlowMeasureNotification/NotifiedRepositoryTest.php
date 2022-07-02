<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\NotifiedRepository;
use Tests\TestCase;

class NotifiedRepositoryTest extends TestCase
{
    private readonly NotifiedRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotifiedRepository();
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
}
