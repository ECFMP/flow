<?php

namespace Tests\Discord\FlowMeasure\Embed;

use App\Discord\FlowMeasure\Embed\ActivatedEmbeds;
use App\Discord\FlowMeasure\Embed\ExpiredEmbeds;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedFactory;
use App\Discord\FlowMeasure\Embed\NotifiedEmbeds;
use App\Discord\FlowMeasure\Embed\WithdrawnEmbeds;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Enums\DiscordNotificationType;
use Mockery;
use Tests\TestCase;

class FlowMeasureEmbedFactoryTest extends TestCase
{
    private readonly FlowMeasureEmbedFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new FlowMeasureEmbedFactory();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testItReturnsEmbeds(DiscordNotificationType $type, string $expectedType)
    {
        $mockMessage = Mockery::mock(PendingMessageInterface::class);
        $mockMessage->shouldReceive('type')->once()->andReturn($type);
        $this->assertEquals($expectedType, get_class($this->factory->make($mockMessage)));
    }

    public function dataProvider(): array
    {
        return [
            'Activated' => [
                DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
                ActivatedEmbeds::class,
            ],
            'Notified' => [
                DiscordNotificationType::FLOW_MEASURE_NOTIFIED,
                NotifiedEmbeds::class,
            ],
            'Expired' => [
                DiscordNotificationType::FLOW_MEASURE_EXPIRED,
                ExpiredEmbeds::class,
            ],
            'Withdrawn' => [
                DiscordNotificationType::FLOW_MEASURE_WITHDRAWN,
                WithdrawnEmbeds::class,
            ],
        ];
    }
}
