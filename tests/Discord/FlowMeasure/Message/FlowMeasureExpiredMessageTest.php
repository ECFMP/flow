<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Message\FlowMeasureExpiredMessage;
use App\Discord\Message\Embed\Colour;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Tests\TestCase;

class FlowMeasureExpiredMessageTest extends TestCase
{
    public function testItHasNoContent()
    {
        $measure = FlowMeasure::factory()->create();

        $this->assertEquals(
            '',
            (new FlowMeasureExpiredMessage($measure))->content()
        );
    }

    public function testItHasEmbeds()
    {
        $measure = FlowMeasure::factory()
            ->withEvent()
            ->finished()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            [
                [
                    'title' => $measure->identifier . ' - ' . 'Expired',
                    'color' => Colour::WITHDRAWN->value,
                    'description' => $measure->event->name,
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
            (new FlowMeasureExpiredMessage($measure))->embeds()->toArray()
        );
    }
}
