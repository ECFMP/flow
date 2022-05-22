<?php

namespace Tests\Discord\FlowMeasure\Description;

use App\Discord\FlowMeasure\Description\EventNameAndInterestedParties;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Tests\TestCase;

class EventNameAndInterestedPartiesTest extends TestCase
{
    private function getContent(FlowMeasure $flowMeasure): string
    {
        return (new EventNameAndInterestedParties($flowMeasure))->description();
    }

    public function testItReturnsInterestedPartiesIfNoEvent()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<' . $tag . '>')->join(' ')
            ),
            $this->getContent($measure)
        );
    }

    public function testItReturnsInterestedPartiesWithAtSymbolIfDiscordTagMissingAndNoEvent()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<@' . $tag . '>')->join(' ')
            ),
            $this->getContent($measure)
        );
    }

    public function testItReturnsBlankIfNoInterestedPartiesAndNoEvent()
    {
        $fir = FlightInformationRegion::factory()->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            '',
            $this->getContent($measure)
        );
    }

    public function testItReturnsInterestedPartiesAndEvent()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure = FlowMeasure::factory()->withEvent()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "%s\n\n**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $measure->event->name,
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<' . $tag . '>')->join(' ')
            ),
            $this->getContent($measure)
        );
    }

    public function testItReturnsInterestedPartiesWithAtSymbolIfDiscordTagMissingAndEvent()
    {
        $fir = FlightInformationRegion::factory()->has(
            DiscordTag::factory()->withoutAtSymbol()->count(2)
        )->create();
        $measure = FlowMeasure::factory()->withEvent()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "%s\n\n**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $measure->event->name,
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<@' . $tag . '>')->join(' ')
            ),
            $this->getContent($measure)
        );
    }

    public function testItReturnsJustEventIfNoInterestedParties()
    {
        $fir = FlightInformationRegion::factory()->create();
        $measure = FlowMeasure::factory()->withEvent()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            $measure->event->name,
            $this->getContent($measure)
        );
    }
}
