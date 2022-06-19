<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\InterestedParties;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Tests\TestCase;

class InterestedPartiesTest extends TestCase
{
    public function testItReturnsInterestedParties()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<' . $tag . '>')->join(' ')
            ),
            InterestedParties::interestedPartiesString($measure)
        );
    }

    public function testItReturnsInterestedPartiesWithAtSymbolIfDiscordTagMissing()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<@' . $tag . '>')->join(' ')
            ),
            InterestedParties::interestedPartiesString($measure)
        );
    }

    public function testItReturnsBlankIfNoInterestedParties()
    {
        $fir = FlightInformationRegion::factory()->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            '',
            InterestedParties::interestedPartiesString($measure)
        );
    }
}
