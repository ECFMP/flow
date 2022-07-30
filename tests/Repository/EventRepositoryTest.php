<?php

namespace Tests\Repository;

use App\Models\Event;
use App\Repository\EventRepository;
use Carbon\Carbon;
use Tests\TestCase;

class EventRepositoryTest extends TestCase
{
    private readonly EventRepository $eventRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->eventRepository = $this->app->make(EventRepository::class);
    }

    public function testItReturnsApiRelevantEvents()
    {
        // Should show
        $upcoming = Event::factory()
            ->notStarted()
            ->create();

        $active = Event::factory()
            ->create();

        $finished = Event::factory()
            ->finished()
            ->create();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();

        // Shouldn't show, deleted.
        $deleted = Event::factory()
            ->finished()
            ->create();
        $deleted->delete();

        $expected = [$upcoming->id, $active->id, $finished->id];
        $actual = $this->eventRepository->getApiRelevantEvents(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsApiRelevantDeletedEvents()
    {
        // Should show
        $upcoming = Event::factory()
            ->notStarted()
            ->create();
        $upcoming->delete();

        $active = Event::factory()
            ->create();

        $finished = Event::factory()
            ->finished()
            ->create();
        $finished->delete();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();

        $expected = [$upcoming->id, $active->id, $finished->id];
        $actual = $this->eventRepository->getApiRelevantEvents(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsActiveEventsWithoutDeleted()
    {
        // Should show
        $active1 = Event::factory()
            ->create();

        $active2 = Event::factory()
            ->create();

        $active3 = Event::factory()
            ->create();

        // Shouldn't show, not started
        Event::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, finished
        Event::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = Event::factory()
            ->create();
        $deleted->delete();

        $expected = [$active1->id, $active2->id, $active3->id];
        $actual = $this->eventRepository->getActiveEvents(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsActiveEventsWithDeleted()
    {
        // Should show
        $active1 = Event::factory()
            ->create();

        $active2 = Event::factory()
            ->create();

        $active3 = Event::factory()
            ->create();

        $deleted = Event::factory()
            ->create();
        $deleted->delete();

        // Shouldn't show, not started
        Event::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, finished
        Event::factory()
            ->finished()
            ->create();

        $expected = [$active1->id, $active2->id, $active3->id, $deleted->id];
        $actual = $this->eventRepository->getActiveEvents(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsUpcomingEventsWithoutDeleted()
    {
        // Should show
        $upcoming1 = Event::factory()
            ->notStarted()
            ->create();

        $upcoming2 = Event::factory()
            ->notStarted()
            ->create();

        $upcoming3 = Event::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, active
        Event::factory()
            ->create();

        // Shouldn't show, finished
        Event::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = Event::factory()
            ->notStarted()
            ->create();
        $deleted->delete();

        $expected = [$upcoming1->id, $upcoming2->id, $upcoming3->id];
        $actual = $this->eventRepository->getUpcomingEvents(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsUpcomingEventsWithDeleted()
    {
        // Should show
        $upcoming1 = Event::factory()
            ->notStarted()
            ->create();

        $upcoming2 = Event::factory()
            ->notStarted()
            ->create();

        $upcoming3 = Event::factory()
            ->notStarted()
            ->create();

        $deleted = Event::factory()
            ->notStarted()
            ->create();
        $deleted->delete();

        // Shouldn't show, active
        Event::factory()
            ->create();

        // Shouldn't show, finished
        Event::factory()
            ->finished()
            ->create();

        $expected = [$upcoming1->id, $upcoming2->id, $upcoming3->id, $deleted->id];
        $actual = $this->eventRepository->getUpcomingEvents(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsUpcomingAndActiveEventsWithoutDeleted()
    {
        // Should show
        $upcoming1 = Event::factory()
            ->notStarted()
            ->create();

        $upcoming2 = Event::factory()
            ->notStarted()
            ->create();

        $active = Event::factory()
            ->create();

        // Shouldn't show, finished
        Event::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = Event::factory()
            ->notStarted()
            ->create();
        $deleted->delete();

        $expected = [$upcoming1->id, $upcoming2->id, $active->id];
        $actual = $this->eventRepository->getActiveAndUpcomingEvents(false)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testItReturnsUpcomingAndActiveEventsWithDeleted()
    {
        // Should show
        $upcoming1 = Event::factory()
            ->notStarted()
            ->create();

        $upcoming2 = Event::factory()
            ->notStarted()
            ->create();

        $active = Event::factory()
            ->create();

        $deletedUpcoming = Event::factory()
            ->notStarted()
            ->create();
        $deletedUpcoming->delete();

        $deletedActive = Event::factory()
            ->create();
        $deletedActive->delete();

        $expected = [$upcoming1->id, $upcoming2->id, $active->id, $deletedUpcoming->id, $deletedActive->id];

        $actual = $this->eventRepository->getActiveAndUpcomingEvents(true)
            ->pluck('id')
            ->sort()
            ->toArray();

        $this->assertEquals($expected, $actual);
    }
}
