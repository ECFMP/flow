<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\ExpiredRepository;
use Tests\TestCase;

class ExpiredRepositoryTest extends TestCase
{
    private readonly ExpiredRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ExpiredRepository();
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
}
