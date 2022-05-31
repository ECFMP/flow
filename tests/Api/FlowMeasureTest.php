<?php

namespace Tests\Api;

use App\Helpers\ApiDateTimeFormatter;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlowMeasureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
        DB::table('users')->delete();
        DB::table('events')->delete();
        DB::table('flight_information_regions')->delete();
    }

    public function testItReturnsNotFoundIfFlowMeasureNotFound()
    {
        $this->get('api/v1/flow-measure/55')
            ->assertNotFound();
    }

    public function testItReturnsNotFoundIfFlowMeasureSoftDeleted()
    {
        $flowMeasure = FlowMeasure::factory()
            ->create();
        $flowMeasure->delete();


        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertNotFound();
    }

    public function testItReturnsAFlowMeasureNoEvent()
    {
        $flowMeasure = FlowMeasure::factory()
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithAnEvent()
    {
        $flowMeasure = FlowMeasure::factory()
            ->withEvent()
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => $flowMeasure->event->id,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithAMandatoryRoute()
    {
        $flowMeasure = FlowMeasure::factory()
            ->withMandatoryRoute()
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'mandatory_route',
                    'value' => ['LOGAN', 'UL612 LAKEY DCT NUGRA'],
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithNotifiedFlightInformationRegions()
    {
        $flowMeasure = FlowMeasure::factory()
            ->create();

        $flowMeasure->notifiedFlightInformationRegions()
            ->sync(FlightInformationRegion::factory()->count(3)->create()->pluck('id')->toArray());

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                ],
                'notified_flight_information_regions' => $flowMeasure->notifiedFlightInformationRegions
                    ->pluck('id')
                    ->toArray(),
            ]);
    }

    public function testItReturnsAFlowMeasureWithALevelAboveFilter()
    {
        $flowMeasure = FlowMeasure::factory()
            ->withLevelAbove(250)
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                    [
                        'type' => 'level_above',
                        'value' => 250,
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithALevelBelowFilter()
    {
        $flowMeasure = FlowMeasure::factory()
            ->withLevelBelow(250)
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                    [
                        'type' => 'level_above',
                        'value' => 250,
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithALevelFilter()
    {
        $flowMeasure = FlowMeasure::factory()
            ->withLevels([250, 260])
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                    [
                        'type' => 'level',
                        'value' => [250, 260],
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithAMemberEventFilter()
    {
        $event = Event::factory()->create();
        $flowMeasure = FlowMeasure::factory()
            ->withMemberEvent($event)
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                    [
                        'type' => 'member_event',
                        'value' => [
                            'event_id' => $event->id,
                            'event_api' => 'testapicode',
                            'event_vatcan' => 'testvatcancode',
                        ],
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsAFlowMeasureWithAMemberNotEventFilter()
    {
        $event = Event::factory()->create();
        $flowMeasure = FlowMeasure::factory()
            ->withMemberNotEvent($event)
            ->create();

        $this->get('api/v1/flow-measure/' . $flowMeasure->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $flowMeasure->id,
                'ident' => $flowMeasure->identifier,
                'event_id' => null,
                'reason' => $flowMeasure->reason,
                'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
                'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
                'measure' => [
                    'type' => 'minimum_departure_interval',
                    'value' => 120,
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**'],
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM'],
                    ],
                    [
                        'type' => 'member_not_event',
                        'value' => [
                            'event_id' => $event->id,
                            'event_api' => 'testapicode',
                            'event_vatcan' => 'testvatcancode',
                        ],
                    ],
                ],
                'notified_flight_information_regions' => [],
            ]);
    }

    public function testItReturnsEmptyNoFlowMeasures()
    {
        $this->get('api/v1/flow-measure')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItIgnoresDeletedFlowMeasures()
    {
        FlowMeasure::factory()
            ->create()
            ->delete();

        FlowMeasure::factory()
            ->withEvent()
            ->create()
            ->delete();

        $this->get('api/v1/flow-measure')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItReturnsAllFlowMeasures()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->create();

        $flowMeasure2 = FlowMeasure::factory()
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

        $this->get('api/v1/flow-measure')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItReturnsNotifiedFlowMeasures()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $flowMeasure2 = FlowMeasure::factory()
            ->notStarted()
            ->withEvent()
            ->create();

        $deleted = FlowMeasure::factory()
            ->notStarted()
            ->withEvent()
            ->create();

        $deleted->delete();

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

        $this->get('api/v1/flow-measure')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItReturnsNotifiedFlowMeasuresWithDeleted()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $flowMeasure2 = FlowMeasure::factory()
            ->notStarted()
            ->withEvent()
            ->create();
        $flowMeasure2->delete();

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

        $this->get('api/v1/flow-measure?notified=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItReturnsRecentlyFinishedFlowMeasures()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $flowMeasure2 = FlowMeasure::factory()
            ->notStarted()
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

        $this->get('api/v1/flow-measure')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItIncludesDeletedFlowMeasuresIfSpecified()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->create();
        $flowMeasure1->delete();

        $flowMeasure2 = FlowMeasure::factory()
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


        $this->get('api/v1/flow-measure?deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItFiltersForActiveMeasuresIfSpecified()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->create();

        FlowMeasure::factory()
            ->withEvent()
            ->notStarted()
            ->create();

        FlowMeasure::factory()
            ->withEvent()
            ->finished()
            ->create();


        $this->get('api/v1/flow-measure?active=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItFiltersForActiveMeasuresIncludingDeleted()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->create();
        $flowMeasure1->delete();

        FlowMeasure::factory()
            ->withEvent()
            ->notStarted()
            ->create();

        FlowMeasure::factory()
            ->withEvent()
            ->finished()
            ->create();


        $this->get('api/v1/flow-measure?active=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItReturnsActiveAndNotifiedFlowMeasures()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $flowMeasure2 = FlowMeasure::factory()
            ->withEvent()
            ->create();

        FlowMeasure::factory()
            ->finished()
            ->create();

        $deleted = FlowMeasure::factory()
            ->create();
        $deleted->delete();

        $this->get('api/v1/flow-measure?active=1&notified=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }

    public function testItReturnsActiveAndNotifiedFlowMeasuresWithDeleted()
    {
        $flowMeasure1 = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $flowMeasure2 = FlowMeasure::factory()
            ->withEvent()
            ->create();
        $flowMeasure2->delete();

        $this->get('api/v1/flow-measure?active=1&notified=1&deleted=1')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $flowMeasure1->id,
                    'ident' => $flowMeasure1->identifier,
                    'event_id' => null,
                    'reason' => $flowMeasure1->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure1->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
                [
                    'id' => $flowMeasure2->id,
                    'ident' => $flowMeasure2->identifier,
                    'event_id' => $flowMeasure2->event->id,
                    'reason' => $flowMeasure2->reason,
                    'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->start_time),
                    'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure2->end_time),
                    'measure' => [
                        'type' => 'minimum_departure_interval',
                        'value' => 120,
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**'],
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM'],
                        ],
                    ],
                    'notified_flight_information_regions' => [],
                ],
            ]);
    }
}
