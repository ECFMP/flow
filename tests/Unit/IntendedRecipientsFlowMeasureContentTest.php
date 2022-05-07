<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Content\IntendedRecipients;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use DB;
use Tests\TestCase;

class IntendedRecipientsFlowMeasureContentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
        DB::table('events')->delete();
        DB::table('flight_information_regions')->delete();
    }

    public function getContent(FlowMeasure $flowMeasure): string
    {
        return (new IntendedRecipients($flowMeasure))->toString();
    }

    public function testItReturnsEmptyStringIfNoNotifiedFlightInformationRegions()
    {
        $this->assertEquals(
            '',
            $this->getContent(FlowMeasure::factory()->create())
        );
    }

    public function testItReturnsEmptyStringIfTheFlightInformationRegionHasNoDiscordTags()
    {
        $fir = FlightInformationRegion::factory()->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            '',
            $this->getContent($measure)
        );
    }

    public function testItReturnsDiscordTags()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                '**FAO**: %s (acknowledge receipt with a :white_check_mark: reaction)',
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<' . $tag . '>')->join(' ')
            ),
            $this->getContent($measure)
        );
    }

    public function testItHandlesDiscordTagsWithNoAtSymbol()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                '**FAO**: %s (acknowledge receipt with a :white_check_mark: reaction)',
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<@' . $tag . '>')->join(' ')
            ),
            $this->getContent($measure)
        );
    }
}
