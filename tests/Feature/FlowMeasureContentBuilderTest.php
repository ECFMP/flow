<?php

namespace Tests\Feature;

use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
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
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(1))->create();
        $measure = FlowMeasure::factory()->state(fn(array $attributes) => [
            'identifier' => 'EGTT06A',
            'reason' => 'Because I said so',
            'start_time' => Carbon::parse('2022-05-06T22:18:00Z'),
            'end_time' => Carbon::parse('2022-05-06T23:10:00Z'),
        ])->afterCreating(function (FlowMeasure $measure) use ($fir) {
            $measure->notifiedFlightInformationRegions()->save($fir);
        })->create();

        $expected = "```\n";
        $expected .= "EGTT06A\n\n";
        $expected .= "Minimum Departure Interval [MDI]: 2 MINS\n";
        $expected .= "ADEP: EG**          DEST: EHAM\n\n";
        $expected .= "VALID: 06/05 2218-2310Z\n\n";
        $expected .= "DUE: Because I said so\n";
        $expected .= "```\n\n";
        $expected .= sprintf(
            "**FAO**: <%s> (acknowledge receipt with a :white_check_mark: reaction)",
            $measure->notifiedFlightInformationRegions->first()->discordTags->first()->tag,
        );

        $this->assertEquals($expected, FlowMeasureContentBuilder::activated($measure)->toString());
    }

    public function testItBuildsWithdrawnMessage()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(1))->create();
        $measure = FlowMeasure::factory()->state(fn(array $attributes) => [
            'identifier' => 'EGTT06A',
            'reason' => 'Because I said so',
            'start_time' => Carbon::parse('2022-05-06T22:18:00Z'),
            'end_time' => Carbon::parse('2022-05-06T23:10:00Z'),
        ])->afterCreating(function (FlowMeasure $measure) use ($fir) {
            $measure->notifiedFlightInformationRegions()->save($fir);
        })->create();

        $expected = "```\n";
        $expected .= "EGTT06A\n\n";
        $expected .= "Minimum Departure Interval [MDI]: 2 MINS\n";
        $expected .= "ADEP: EG**          DEST: EHAM\n";
        $expected .= "```\n\n";
        $expected .= sprintf(
            "**FAO**: <%s> (acknowledge receipt with a :white_check_mark: reaction)",
            $measure->notifiedFlightInformationRegions->first()->discordTags->first()->tag,
        );

        $this->assertEquals($expected, FlowMeasureContentBuilder::withdrawn($measure)->toString());
    }

    public function testItBuildsExpiredMessage()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(1))->create();
        $measure = FlowMeasure::factory()->state(fn(array $attributes) => [
            'identifier' => 'EGTT06A',
            'reason' => 'Because I said so',
            'start_time' => Carbon::parse('2022-05-06T22:18:00Z'),
            'end_time' => Carbon::parse('2022-05-06T23:10:00Z'),
        ])->afterCreating(function (FlowMeasure $measure) use ($fir) {
            $measure->notifiedFlightInformationRegions()->save($fir);
        })->create();

        $expected = "```\n";
        $expected .= "EGTT06A\n\n";
        $expected .= "Minimum Departure Interval [MDI]: 2 MINS\n";
        $expected .= "ADEP: EG**          DEST: EHAM\n";
        $expected .= "```";

        $this->assertEquals($expected, FlowMeasureContentBuilder::expired($measure)->toString());
    }
}
