<?php

namespace Tests\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\WithdrawnRepository;
use Carbon\Carbon;
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
            DiscordNotificationType::FLOW_MEASURE_WITHDRAWN,
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
}
