<?php

namespace Tests\Feature;

use App\Helpers\ApiDateTimeFormatter;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventApiTest extends TestCase
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
                ]
            );
    }

    public function testItReturnsEmptyIfNoEvents()
    {
        $this->get('api/v1/event')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItReturnsAllEvents()
    {
        $event1 = Event::factory()->create();
        $event2 = Event::factory()->create();

        $this->get('api/v1/event')
            ->assertOk()
            ->assertExactJson([
                    [
                        'id' => $event1->id,
                        'name' => $event1->name,
                        'date_start' => ApiDateTimeFormatter::formatDateTime($event1->date_start),
                        'date_end' => ApiDateTimeFormatter::formatDateTime($event1->date_end),
                        'flight_information_region_id' => $event1->flight_information_region_id,
                        'vatcan_code' => null,
                    ],
                    [
                        'id' => $event2->id,
                        'name' => $event2->name,
                        'date_start' => ApiDateTimeFormatter::formatDateTime($event2->date_start),
                        'date_end' => ApiDateTimeFormatter::formatDateTime($event2->date_end),
                        'flight_information_region_id' => $event2->flight_information_region_id,
                        'vatcan_code' => null,
                    ],
                ]
            );
    }

    public function testItReturnsOnlyActiveEvents()
    {
        $event = Event::factory()->create();
        Event::factory()->notStarted()->create();
        Event::factory()->finished()->create();

        $this->get('api/v1/event?active=1')
            ->assertOk()
            ->assertExactJson([
                    [
                        'id' => $event->id,
                        'name' => $event->name,
                        'date_start' => ApiDateTimeFormatter::formatDateTime($event->date_start),
                        'date_end' => ApiDateTimeFormatter::formatDateTime($event->date_end),
                        'flight_information_region_id' => $event->flight_information_region_id,
                        'vatcan_code' => null,
                    ],
                ]
            );
    }
}
