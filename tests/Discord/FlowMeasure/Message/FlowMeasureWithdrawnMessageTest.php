<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Description\EventNameAndInterestedParties;
use App\Discord\FlowMeasure\Message\FlowMeasureWithdrawnMessage;
use App\Discord\Message\Embed\Colour;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Tests\TestCase;

class FlowMeasureWithdrawnMessageTest extends TestCase
{
    public function testItHasNoContent()
    {
        $measure = FlowMeasure::factory()->create();

        $this->assertEquals(
            '',
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
                    'description' => (new EventNameAndInterestedParties($measure))->description(),
                    'fields' => collect([
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
                    ]),
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
                    'description' => (new EventNameAndInterestedParties($measure))->description(),
                    'fields' => collect([
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
                    ]),
                ],
            ],
            (new FlowMeasureWithdrawnMessage($measure))->embeds()->toArray()
        );
    }
}
