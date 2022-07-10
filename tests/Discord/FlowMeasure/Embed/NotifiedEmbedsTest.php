<?php

namespace Tests\Discord\FlowMeasure\Embed;

use App\Discord\FlowMeasure\Description\EventName;
use App\Discord\FlowMeasure\Embed\NotifiedEmbeds;
use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\Message\Embed\Colour;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class NotifiedEmbedsTest extends TestCase
{
    private readonly PendingMessageInterface $pendingMessage;
    private readonly NotificationReissuerInterface $reissuer;
    private readonly NotifiedEmbeds $embeds;

    public function setUp(): void
    {
        parent::setUp();
        $this->pendingMessage = Mockery::mock(PendingMessageInterface::class);
        $this->reissuer = Mockery::mock(NotificationReissuerInterface::class);
        $this->embeds = new NotifiedEmbeds($this->pendingMessage);
    }

    public function testItHasEmbeds()
    {
        $measure = FlowMeasure::factory()
            ->withTimes(Carbon::parse('2022-05-22T14:54:23Z'), Carbon::parse('2022-05-22T16:37:22Z'))
            ->withEvent()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();

        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->pendingMessage->shouldReceive('flowMeasure')->andReturn($measure);
        $this->pendingMessage->shouldReceive('reissue')->andReturn($this->reissuer);
        $this->reissuer->shouldReceive('isReissuedNotification')->andReturn(false);

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
            $this->embeds->embeds()->toArray()
        );
    }

    public function testItHasEmbedsWhenReissued()
    {
        $measure = FlowMeasure::factory()
            ->withTimes(Carbon::parse('2022-05-22T14:54:23Z'), Carbon::parse('2022-05-22T16:37:22Z'))
            ->withEvent()
            ->withAdditionalFilter(['type' => 'level_below', 'value' => 220])->create();

        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->count(2))->create();
        $measure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $this->pendingMessage->shouldReceive('flowMeasure')->andReturn($measure);
        $this->pendingMessage->shouldReceive('reissue')->andReturn($this->reissuer);
        $this->reissuer->shouldReceive('isReissuedNotification')->andReturn(true);

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
            $this->embeds->embeds()->toArray()
        );
    }
}
