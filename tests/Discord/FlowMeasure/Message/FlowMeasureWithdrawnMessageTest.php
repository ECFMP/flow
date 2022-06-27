<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Description\EventName;
use App\Discord\FlowMeasure\Message\FlowMeasureWithdrawnMessage;
use App\Discord\Message\Embed\Colour;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Tests\TestCase;

class FlowMeasureWithdrawnMessageTest extends TestCase
{
    public function testItHasFaoContent()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure = FlowMeasure::factory()->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            sprintf(
                "**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
                $fir->discordTags->pluck('tag')->map(fn(string $tag) => '<' . $tag . '>')->join(' ')
            ),
            (new FlowMeasureWithdrawnMessage($measure))->content()
        );
    }

    public function testItHasEmbedsWhenWithinActivePeriod()
    {
        $measure = FlowMeasure::factory()
            ->withEvent()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $measure->delete();

        $this->assertEquals(
            [
                [
                    'title' => $measure->identifier . ' - ' . 'Withdrawn',
                    'color' => Colour::WITHDRAWN->value,
                    'description' => (new EventName($measure))->description(),
                    'fields' => [
                        [
                            'name' => 'Minimum Departure Interval [MDI]',
                            'value' => '2 Minutes',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Departure Airports',
                            'value' => 'EG**',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Arrival Airports',
                            'value' => 'EHAM',
                            'inline' => true,
                        ],
                    ],
                ],
            ],
            (new FlowMeasureWithdrawnMessage($measure))->embeds()->toArray()
        );
    }

    public function testItHasEmbedsWhenExpired()
    {
        $measure = FlowMeasure::factory()
            ->withEvent()
            ->finished()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $measure->delete();

        $this->assertEquals(
            [
                [
                    'title' => $measure->identifier . ' - ' . 'Withdrawn',
                    'color' => Colour::WITHDRAWN->value,
                    'description' => (new EventName($measure))->description(),
                    'fields' => [
                        [
                            'name' => 'Minimum Departure Interval [MDI]',
                            'value' => '2 Minutes',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Departure Airports',
                            'value' => 'EG**',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Arrival Airports',
                            'value' => 'EHAM',
                            'inline' => true,
                        ],
                    ],
                ],
            ],
            (new FlowMeasureWithdrawnMessage($measure))->embeds()->toArray()
        );
    }
}
