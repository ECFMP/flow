<?php

namespace Tests\Feature;

use App\Helpers\ApiDateTimeFormatter;
use App\Models\FlowMeasure;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlowMeasureApiTest extends TestCase
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
                    'value' => 120
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**']
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM']
                    ],
                ]
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
                    'value' => 120
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**']
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM']
                    ],
                ]
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
                    'value' => json_encode(['LOGAN', 'UL612 LAKEY DCT NUGRA']),
                ],
                'filters' => [
                    [
                        'type' => 'ADEP',
                        'value' => ['EG**']
                    ],
                    [
                        'type' => 'ADES',
                        'value' => ['EHAM']
                    ],
                ]
            ]);
    }

    public function testItReturnsEmptyNoFlowMeasures()
    {
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
                        'value' => 120
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**']
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM']
                        ],
                    ]
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
                        'value' => 120
                    ],
                    'filters' => [
                        [
                            'type' => 'ADEP',
                            'value' => ['EG**']
                        ],
                        [
                            'type' => 'ADES',
                            'value' => ['EHAM']
                        ],
                    ]
                ]
            ]);
    }
}
