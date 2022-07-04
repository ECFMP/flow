<?php

namespace Tests\Discord\FlowMeasure\Embed;

use App\Discord\FlowMeasure\Description\EventName;
use App\Discord\FlowMeasure\Embed\ExpiredEmbeds;
use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\Message\Embed\Colour;
use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class ExpiredEmbedsTest extends TestCase
{
    private readonly PendingMessageInterface $pendingMessage;
    private readonly NotificationReissuerInterface $reissuer;
    private readonly ExpiredEmbeds $embeds;

    public function setUp(): void
    {
        parent::setUp();
        $this->pendingMessage = Mockery::mock(PendingMessageInterface::class);
        $this->embeds = new ExpiredEmbeds($this->pendingMessage);
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

        $this->assertEquals(
            [
                [
                    'title' => $measure->identifier . ' - ' . 'Expired',
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
            $this->embeds->embeds()->toArray()
        );
    }
}
