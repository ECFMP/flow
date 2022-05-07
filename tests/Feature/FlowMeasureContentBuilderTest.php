<?php

namespace Tests\Feature;

use App\Models\FlowMeasure;
use App\Service\FlowMeasureContentBuilder;
use Carbon\Carbon;
use DB;
use Tests\TestCase;

class FlowMeasureContentBuilderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    public function testItBuildsActivatedMessage()
    {
        $measure = FlowMeasure::factory()->state(fn(array $attributes) => [
            'identifier' => 'EGTT06A',
            'reason' => 'Because I said so',
            'start_time' => Carbon::parse('2022-05-06T22:18:00Z'),
            'end_time' => Carbon::parse('2022-05-06T23:10:00Z'),
        ])->create();

        $expected = "```\n";
        $expected .= "EGTT06A\n\n";
        $expected .= "Minimum Departure Interval [MDI]: 2 MINS\n";
        $expected .= "ADEP: EG**          DEST: EHAM\n\n";
        $expected .= "VALID: 06/05 2218-2310Z\n\n";
        $expected .= "DUE: Because I said so\n";
        $expected .= "```\n\n";

        $this->assertEquals($expected, FlowMeasureContentBuilder::activated($measure)->toString());
    }

    public function testItBuildsWithdrawnMessage()
    {
        $measure = FlowMeasure::factory()->state(fn(array $attributes) => [
            'identifier' => 'EGTT06A',
            'reason' => 'Because I said so',
            'start_time' => Carbon::parse('2022-05-06T22:18:00Z'),
            'end_time' => Carbon::parse('2022-05-06T23:10:00Z'),
        ])->create();

        $expected = "```\n";
        $expected .= "EGTT06A\n\n";
        $expected .= "Minimum Departure Interval [MDI]: 2 MINS\n";
        $expected .= "ADEP: EG**          DEST: EHAM\n";
        $expected .= "```\n\n";

        $this->assertEquals($expected, FlowMeasureContentBuilder::withdrawn($measure)->toString());
    }
}
