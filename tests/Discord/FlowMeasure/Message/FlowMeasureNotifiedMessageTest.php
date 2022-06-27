<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Description\EventName;
use App\Discord\FlowMeasure\Message\FlowMeasureNotifiedMessage;
use App\Discord\Message\Embed\Colour;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Tests\TestCase;

class FlowMeasureNotifiedMessageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2022-05-22T13:54:23Z'));
    }

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
            (new FlowMeasureNotifiedMessage($measure, false))->content()
        );
    }

    public function testItHasEmbeds()
    {
        $measure = FlowMeasure::factory()
            ->notStarted()
            ->withTimes(Carbon::parse('2022-05-22T14:54:23Z'), Carbon::parse('2022-05-22T16:37:22Z'))
            ->withEvent()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();

        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            [
                [
                    'title' => $measure->identifier . ' - ' . 'Notified',
                    'color' => Colour::NOTIFIED->value,
                    'description' => (new EventName($measure))->description(),
                    'fields' => [
                        [
                            'name' => 'Minimum Departure Interval [MDI]',
                            'value' => '2 Minutes',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Start Time',
                            'value' => '22/05 1454Z',
                            'inline' => true,
                        ],
                        [
                            'name' => 'End Time',
                            'value' => '1637Z',
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
                        [
                            'name' => "\u{200b}",
                            'value' => "\u{200b}",
                            'inline' => true,
                        ],
                        [
                            'name' => 'Level at or Below',
                            'value' => '220',
                            'inline' => false,
                        ],
                        [
                            'name' => 'Reason',
                            'value' => $measure->reason,
                            'inline' => false,
                        ],
                    ],
                ],
            ],
            (new FlowMeasureNotifiedMessage($measure, false))->embeds()->toArray()
        );
    }

    public function testItHasEmbedsWhenReissued()
    {
        $measure = FlowMeasure::factory()
            ->notStarted()
            ->withTimes(Carbon::parse('2022-05-22T14:54:23Z'), Carbon::parse('2022-05-22T16:37:22Z'))
            ->withEvent()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();

        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->assertEquals(
            [
                [
                    'title' => $measure->identifier . ' - ' . 'Notified (Reissued)',
                    'color' => Colour::NOTIFIED->value,
                    'description' => (new EventName($measure))->description(),
                    'fields' => [
                        [
                            'name' => 'Minimum Departure Interval [MDI]',
                            'value' => '2 Minutes',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Start Time',
                            'value' => '22/05 1454Z',
                            'inline' => true,
                        ],
                        [
                            'name' => 'End Time',
                            'value' => '1637Z',
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
                        [
                            'name' => "\u{200b}",
                            'value' => "\u{200b}",
                            'inline' => true,
                        ],
                        [
                            'name' => 'Level at or Below',
                            'value' => '220',
                            'inline' => false,
                        ],
                        [
                            'name' => 'Reason',
                            'value' => $measure->reason,
                            'inline' => false,
                        ],
                    ],
                ],
            ],
            (new FlowMeasureNotifiedMessage($measure, true))->embeds()->toArray()
        );
    }
}
