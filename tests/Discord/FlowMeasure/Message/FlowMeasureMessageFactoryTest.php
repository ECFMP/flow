<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsFactory;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedFactory;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\FlowMeasure\Provider\PendingWebhookMessageInterface;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Mockery;
use Tests\TestCase;

class FlowMeasureMessageFactoryTest extends TestCase
{
    private readonly EmbedCollection $embedCollection;
    private readonly FlowMeasureEmbedInterface $embeds;
    private readonly FlowMeasureEmbedFactory $embedFactory;
    private readonly FlowMeasureRecipientsInterface $recipients;
    private readonly FlowMeasureRecipientsFactory $recipientsFactory;
    private readonly FlowMeasureMessageFactory $factory;
    private readonly PendingWebhookMessageInterface $pendingWebhookMessage;
    private readonly WebhookInterface $webhook;

    private readonly PendingMessageInterface $pendingEcfmpMessage;

    public function setUp(): void
    {
        parent::setUp();
        $flowMeasure = FlowMeasure::factory()->make();
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->pendingWebhookMessage = Mockery::mock(PendingWebhookMessageInterface::class);
        $this->pendingWebhookMessage->shouldReceive('webhook')->andReturn($this->webhook);
        $this->pendingWebhookMessage->shouldReceive('flowMeasure')->andReturn($flowMeasure);
        $this->pendingWebhookMessage->shouldReceive('type')->andReturn(DiscordNotificationType::FLOW_MEASURE_WITHDRAWN);
        $this->embedCollection = new EmbedCollection();
        $this->embeds = Mockery::mock(FlowMeasureEmbedInterface::class);
        $this->embeds->shouldReceive('embeds')->andReturn($this->embedCollection);
        $this->embedFactory = Mockery::mock(FlowMeasureEmbedFactory::class);
        $this->embedFactory->shouldReceive('make')->andReturn($this->embeds);
        $this->recipients = Mockery::mock(FlowMeasureRecipientsInterface::class);
        $this->recipients->shouldReceive('toString')->andReturn('foo');
        $this->recipientsFactory = Mockery::mock(FlowMeasureRecipientsFactory::class);
        $this->recipientsFactory->shouldReceive('makeRecipients')->andReturn($this->recipients);
        $this->factory = new FlowMeasureMessageFactory($this->recipientsFactory, $this->embedFactory);
        $this->pendingEcfmpMessage = Mockery::mock(PendingMessageInterface::class);
        $this->pendingEcfmpMessage->shouldReceive('flowMeasure')->andReturn(FlowMeasure::factory()->make());
        $this->pendingEcfmpMessage->shouldReceive('type')->andReturn(DiscordNotificationType::FLOW_MEASURE_WITHDRAWN);
        $this->recipientsFactory->shouldReceive('makeEcfmpRecipients')->andReturn($this->recipients);
    }

    public function testItMakesAMessage()
    {
        $message = $this->factory->make($this->pendingWebhookMessage);
        $this->assertEquals($this->webhook, $message->destination());
        $this->assertEquals('foo', $message->content());
        $this->assertEquals($this->embedCollection, $message->embeds());
    }

    public function testItMakesAnEcfmpMessage()
    {
        $message = $this->factory->makeEcfmp($this->pendingEcfmpMessage);
        $this->assertEquals(config('discord.ecfmp_channel_id'), $message->channel());
        $this->assertEquals('foo', $message->content());
        $this->assertEquals($this->embedCollection, $message->embeds());
    }
}
