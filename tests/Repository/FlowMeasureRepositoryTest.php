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

        // Shouldn't show, deleted.
        $deleted = FlowMeasure::factory()
            ->finished()
            ->withEvent()
            ->create();
        $deleted->delete();

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

    public function testItReturnsActiveFlowMeasuresWithoutDeleted()
    {
        // Should show
        $active1 = FlowMeasure::factory()
            ->create();

        $active2 = FlowMeasure::factory()
            ->create();

        $active3 = FlowMeasure::factory()
            ->create();

        // Shouldn't show, not started
        FlowMeasure::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, finished
        FlowMeasure::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = FlowMeasure::factory()
            ->create();
        $deleted->delete();

        $expected = [$active1->id, $active2->id, $active3->id];
        $actual = $this->flowMeasureRepository->getActiveFlowMeasures(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsActiveFlowMeasuresWithDeleted()
    {
        // Should show
        $active1 = FlowMeasure::factory()
            ->create();

        $active2 = FlowMeasure::factory()
            ->create();

        $active3 = FlowMeasure::factory()
            ->create();

        $deleted = FlowMeasure::factory()
            ->create();
        $deleted->delete();

        // Shouldn't show, not started
        FlowMeasure::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, finished
        FlowMeasure::factory()
            ->finished()
            ->create();

        $expected = [$active1->id, $active2->id, $active3->id, $deleted->id];
        $actual = $this->flowMeasureRepository->getActiveFlowMeasures(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsNotifiedFlowMeasuresWithoutDeleted()
    {
        // Should show
        $notified1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $notified2 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $notified3 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, active
        FlowMeasure::factory()
            ->create();

        // Shouldn't show, finished
        FlowMeasure::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = FlowMeasure::factory()
            ->notStarted()
            ->create();
        $deleted->delete();

        $expected = [$notified1->id, $notified2->id, $notified3->id];
        $actual = $this->flowMeasureRepository->getNotifiedFlowMeasures(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsNotifiedFlowMeasuresWithDeleted()
    {
        // Should show
        $notified1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $notified2 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $notified3 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $deleted = FlowMeasure::factory()
            ->notStarted()
            ->create();
        $deleted->delete();

        // Shouldn't show, active
        FlowMeasure::factory()
            ->create();

        // Shouldn't show, finished
        FlowMeasure::factory()
            ->finished()
            ->create();

        $expected = [$notified1->id, $notified2->id, $notified3->id, $deleted->id];
        $actual = $this->flowMeasureRepository->getNotifiedFlowMeasures(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsNotifiedAndActiveFlowMeasuresWithoutDeleted()
    {
        // Should show
        $notified1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $notified2 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $active = FlowMeasure::factory()
            ->create();

        // Shouldn't show, finished
        FlowMeasure::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = FlowMeasure::factory()
            ->notStarted()
            ->create();
        $deleted->delete();

        $expected = [$notified1->id, $notified2->id, $active->id];
        $actual = $this->flowMeasureRepository->getActiveAndNotifiedFlowMeasures(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsNotifiedAndActiveFlowMeasuresWithDeleted()
    {
        // Should show
        $notified1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $notified2 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $active = FlowMeasure::factory()
            ->create();

        $deletedNotified = FlowMeasure::factory()
            ->notStarted()
            ->create();
        $deletedNotified->delete();

        $deletedActive = FlowMeasure::factory()
            ->create();
        $deletedActive->delete();

        $expected = [$notified1->id, $notified2->id, $active->id, $deletedNotified->id, $deletedActive->id];

        $actual = $this->flowMeasureRepository->getActiveAndNotifiedFlowMeasures(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsMeasuresActiveDuringATimePeriod()
    {
        $start = Carbon::parse('2022-08-19 20:00:00');
        $end = Carbon::parse('2022-08-19 21:00:00');

        $startsDuring = FlowMeasure::factory()
            ->withTimes($start->clone()->addMinute(), $end->clone()->addHour())
            ->create();

        $endsDuring = FlowMeasure::factory()
            ->withTimes($start->clone()->subMinutes(30), $end->clone()->subMinutes(15))
            ->create();

        $throughout = FlowMeasure::factory()
            ->withTimes($start->clone()->subMinutes(30), $end->clone()->addMinutes(15))
            ->create();

        // Skip these

        // Starts during - deleted
        FlowMeasure::factory()
            ->withTimes($start->clone()->addMinute(), $end->clone()->addHour())
            ->afterCreating(function (FlowMeasure $flowMeasure) {
                $flowMeasure->delete();
            })
            ->create();

        // Ends during - deleted
        FlowMeasure::factory()
            ->withTimes($start->clone()->subMinutes(30), $end->clone()->subMinutes(15))
            ->afterCreating(function (FlowMeasure $flowMeasure) {
                $flowMeasure->delete();
            })
            ->create();

        // Throughout - deleted
        FlowMeasure::factory()
            ->withTimes($start->clone()->subMinutes(30), $end->clone()->addMinutes(15))
            ->afterCreating(function (FlowMeasure $flowMeasure) {
                $flowMeasure->delete();
            })
            ->create();

        // Ends before
        FlowMeasure::factory()
            ->withTimes($start->clone()->subMinutes(30), $start->clone()->subMinute())
            ->create();

        // Starts after
        FlowMeasure::factory()
            ->withTimes($end->clone()->addMinute(), $end->clone()->addMinutes(30))
            ->create();

        $this->assertEquals(
            [$startsDuring->id, $endsDuring->id, $throughout->id],
            $this->flowMeasureRepository->getFlowMeasuresActiveDuringPeriod($start, $end)
                ->pluck('id')
                ->toArray()
        );
    }
}
