<?php

namespace Tests\Api;

use App\Helpers\ApiDateTimeFormatter;
use App\Models\Event;
use App\Models\EventParticipant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
        DB::table('events')->delete();
        DB::table('flight_information_regions')->delete();
    }

    public function testItReturnsNotFoundIfEventNotFound()
    {
        $this->get('api/v1/event/55')
            ->assertNotFound();
    }

    public function testItReturnsAnEvent()
    {
        $event = Event::factory()->create();

        $this->get('api/v1/event/' . $event->id)
            ->assertOk()
            ->assertExactJson(
                [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event->date_end),
                    'flight_information_region_id' => $event->flight_information_region_id,
                    'vatcan_code' => null,
                    'participants' => [],
                ]
            );
    }

    public function testItReturnsAnEventWithAVatcanCode()
    {
        $event = Event::factory()->withVatcanCode()->create();

        $this->get('api/v1/event/' . $event->id)
            ->assertOk()
            ->assertExactJson(
                [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event->date_end),
                    'flight_information_region_id' => $event->flight_information_region_id,
                    'vatcan_code' => $event->vatcan_code,
                    'participants' => [],
                ]
            );
    }

    public function testItReturnsAnEventWithParticipants()
    {
        $event = Event::factory()->withParticipants()->create();

        $this->get('api/v1/event/' . $event->id)
            ->assertOk()
            ->assertExactJson(
                [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event->date_end),
                    'flight_information_region_id' => $event->flight_information_region_id,
                    'vatcan_code' => $event->vatcan_code,
                    'participants' => $event->participants->map(fn(EventParticipant $eventParticipant) => [
                        'cid' => $eventParticipant->cid,
                        'destination' => $eventParticipant->destination,
                        'origin' => $eventParticipant->origin,
                    ])
                ]
            );
    }

    public function testItReturnsEmptyNoEvents()
    {
        $this->get('api/v1/event')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItIgnoresDeletedEvents()
    {
        Event::factory()
            ->create()
            ->delete();

        Event::factory()
            ->create()
            ->delete();

        $this->get('api/v1/flow-measure')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItReturnsAllEvents()
    {
        $event1 = Event::factory()
            ->create();

        $event2 = Event::factory()
            ->create();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();

        $this->get('api/v1/event')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsUpcomingEvents()
    {
        $event1 = Event::factory()
            ->notStarted()
            ->create();

        $event2 = Event::factory()
            ->notStarted()
            ->create();

        $deleted = Event::factory()
            ->notStarted()
            ->create();

        $deleted->delete();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();

        $this->get('api/v1/event')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsUpcomingEventsWithDeleted()
    {
        $event1 = Event::factory()
            ->notStarted()
            ->create();

        $event2 = Event::factory()
            ->notStarted()
            ->create();
        $event2->delete();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();

        $this->get('api/v1/event?upcoming=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsRecentlyFinishedEvents()
    {
        $event1 = Event::factory()
            ->notStarted()
            ->create();

        $event2 = Event::factory()
            ->notStarted()
            ->create();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();

        $this->get('api/v1/event')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItIncludesDeletedEventsIfSpecified()
    {
        $event1 = Event::factory()
            ->create();
        $event1->delete();

        $event2 = Event::factory()
            ->create();

        // Shouldn't show, too far in the future
        Event::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->create();

        // Shouldn't show, too far in the past
        Event::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->create();


        $this->get('api/v1/event?deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItFiltersForActiveEventsIfSpecified()
    {
        $event1 = Event::factory()
            ->create();

        Event::factory()
            ->notStarted()
            ->create();

        Event::factory()
            ->finished()
            ->create();


        $this->get('api/v1/event?active=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItFiltersForActiveEventsIncludingDeleted()
    {
        $event1 = Event::factory()
            ->create();
        $event1->delete();

        Event::factory()
            ->notStarted()
            ->create();

        Event::factory()
            ->finished()
            ->create();


        $this->get('api/v1/event?active=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsActiveAndUpcomingEvents()
    {
        $event1 = Event::factory()
            ->notStarted()
            ->create();

        $event2 = Event::factory()
            ->create();

        Event::factory()
            ->finished()
            ->create();

        $deleted = Event::factory()
            ->create();
        $deleted->delete();

        $this->get('api/v1/event?active=1&upcoming=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsActiveAndUpcomingEventsWithDeleted()
    {
        $event1 = Event::factory()
            ->notStarted()
            ->create();

        $event2 = Event::factory()
            ->create();
        $event2->delete();

        $this->get('api/v1/event?active=1&upcoming=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsFinishedEventsWithoutDeleted()
    {
        // Should show
        $event1 = Event::factory()
            ->finished()
            ->create();

        $event2 = Event::factory()
            ->finished()
            ->create();

        // Should not show - active
        Event::factory()
            ->create();

        // Should not show - upcoming
        Event::factory()
            ->notStarted()
            ->create();

        // Shouldn't show - deleted
        $deleted = Event::factory()
            ->finished()
            ->create();
        $deleted->delete();

        $this->get('api/v1/event?finished=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }

    public function testItReturnsFinishedEventsWithDeleted()
    {
        // Should show
        $event1 = Event::factory()
            ->finished()
            ->create();

        $event2 = Event::factory()
            ->finished()
            ->create();

        // Should not show - active
        Event::factory()
            ->create();

        // Should not show - upcoming
        Event::factory()
            ->notStarted()
            ->create();

        // Shouldn't show - deleted
        $deleted = Event::factory()
            ->finished()
            ->create();
        $deleted->delete();

        $this->get('api/v1/event?finished=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $event1->id,
                    'name' => $event1->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                    'flight_information_region_id' => $event1->flight_information_region_id,
                    'vatcan_code' => $event1->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $event2->id,
                    'name' => $event2->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                    'flight_information_region_id' => $event2->flight_information_region_id,
                    'vatcan_code' => $event2->vatcan_code,
                    'participants' => [],
                ],
                [
                    'id' => $deleted->id,
                    'name' => $deleted->name,
                    'date_start' => ApiDateTimeFormatter::formatDateTime($deleted->date_start),
                    'date_end' => ApiDateTimeFormatter::formatDateTime($deleted->date_end),
                    'flight_information_region_id' => $deleted->flight_information_region_id,
                    'vatcan_code' => $deleted->vatcan_code,
                    'participants' => [],
                ],
            ]);
    }
}
