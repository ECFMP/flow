<?php

namespace Tests\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Provider\MessageProvider;
use App\Discord\FlowMeasure\Webhook\WebhookMapper;
use App\Enums\DiscordNotificationType;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\RepositoryInterface;
use Mockery;
use Tests\TestCase;

class MessageProviderTest extends TestCase
{
    private readonly RepositoryInterface $repository;
    private readonly WebhookMapper $mapper;
    private readonly MessageProvider $messageProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(RepositoryInterface::class);
        $this->mapper = Mockery::mock(WebhookMapper::class);
        $this->messageProvider = new MessageProvider($this->repository, $this->mapper);
    }

    public function testItProvidesPendingMessages()
    {
        $this->repository->shouldReceive('notificationType')
            ->andReturn(DiscordNotificationType::FLOW_MEASURE_ACTIVATED);

        $measure1 = FlowMeasure::factory()->make();
        $measure2 = FlowMeasure::factory()->make();

        $this->repository->shouldReceive('flowMeasuresForNotification')
            ->once()
            ->andReturn(collect([$measure1, $measure2]));

        $webhook1 = DivisionDiscordWebhook::factory()->make();
        $webhook2 = DivisionDiscordWebhook::factory()->make();

        $this->mapper->shouldReceive('mapToWebhooks')
            ->with($measure1)
            ->once()
            ->andReturn(collect([$webhook1, $webhook2]));

        $this->mapper->shouldReceive('mapToWebhooks')
            ->with($measure2)
            ->once()
            ->andReturn(collect([$webhook2]));

        $pendingMessages = $this->messageProvider->pendingMessages()->toArray();
        $this->assertCount(3, $pendingMessages);

        $this->assertEquals($measure1, $pendingMessages[0]->flowMeasure());
        $this->assertEquals(DiscordNotificationType::FLOW_MEASURE_ACTIVATED, $pendingMessages[0]->type());
        $this->assertEquals($webhook1, $pendingMessages[0]->webhook());
        $this->assertEquals($measure1, $pendingMessages[0]->reissue()->measure());
        $this->assertEquals(DiscordNotificationType::FLOW_MEASURE_ACTIVATED, $pendingMessages[0]->reissue()->type());

        $this->assertEquals($measure1, $pendingMessages[1]->flowMeasure());
        $this->assertEquals(DiscordNotificationType::FLOW_MEASURE_ACTIVATED, $pendingMessages[1]->type());
        $this->assertEquals($webhook2, $pendingMessages[1]->webhook());
        $this->assertEquals($measure1, $pendingMessages[1]->reissue()->measure());
        $this->assertEquals(DiscordNotificationType::FLOW_MEASURE_ACTIVATED, $pendingMessages[1]->reissue()->type());

        $this->assertEquals($measure2, $pendingMessages[2]->flowMeasure());
        $this->assertEquals(DiscordNotificationType::FLOW_MEASURE_ACTIVATED, $pendingMessages[0]->type());
        $this->assertEquals($webhook2, $pendingMessages[2]->webhook());
        $this->assertEquals($measure2, $pendingMessages[2]->reissue()->measure());
        $this->assertEquals(DiscordNotificationType::FLOW_MEASURE_ACTIVATED, $pendingMessages[2]->reissue()->type());
    }
}
