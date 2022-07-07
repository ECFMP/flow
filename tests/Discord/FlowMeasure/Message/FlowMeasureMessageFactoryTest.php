<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsFactory;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedFactory;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Webhook\WebhookInterface;
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
    private readonly PendingMessageInterface $pendingMessage;
    private readonly WebhookInterface $webhook;

    public function setUp(): void
    {
        parent::setUp();
        $flowMeasure = FlowMeasure::factory()->make();
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->pendingMessage = Mockery::mock(PendingMessageInterface::class);
        $this->pendingMessage->shouldReceive('webhook')->andReturn($this->webhook);
        $this->pendingMessage->shouldReceive('flowMeasure')->andReturn($flowMeasure);
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
    }

    public function testItMakesAMessage()
    {
        $message = $this->factory->make($this->pendingMessage);
        $this->assertEquals($this->webhook, $message->destination());
        $this->assertEquals('foo', $message->content());
        $this->assertEquals($this->embedCollection, $message->embeds());
    }
}
