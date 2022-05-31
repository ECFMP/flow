<?php

namespace Tests\Repository;

use App\Models\FlowMeasure;
use App\Repository\FlowMeasureRepository;
use Carbon\Carbon;
use Tests\TestCase;

class FlowMeasureRepositoryTest extends TestCase
{
    private readonly FlowMeasureRepository $flowMeasureRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->flowMeasureRepository = $this->app->make(FlowMeasureRepository::class);
    }

    public function testItReturnsApiRelevantFlowMeasures()
    {
        // Should show
        $notified = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $active = FlowMeasure::factory()
            ->create();

        $finished = FlowMeasure::factory()
            ->finished()
            ->withEvent()
            ->create();

        // Shouldn't show, too far in the future
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->withEvent()
            ->create();

        // Shouldn't show, too far in the past
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->withEvent()
            ->create();

        $expected = [$notified->id, $active->id, $finished->id];
        $actual = $this->flowMeasureRepository->getApiRelevantFlowMeasures(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsApiRelevantDeletedFlowMeasures()
    {
        // Should show
        $notified = FlowMeasure::factory()
            ->notStarted()
            ->create();
        $notified->delete();

        $active = FlowMeasure::factory()
            ->create();
        $active->delete();

        $finished = FlowMeasure::factory()
            ->finished()
            ->withEvent()
            ->create();
        $finished->delete();

        // Shouldn't show, too far in the future
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->withEvent()
            ->create();

        // Shouldn't show, too far in the past
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->withEvent()
            ->create();

        $expected = [$notified->id, $active->id, $finished->id];
        $actual = $this->flowMeasureRepository->getApiRelevantFlowMeasures(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }
}
