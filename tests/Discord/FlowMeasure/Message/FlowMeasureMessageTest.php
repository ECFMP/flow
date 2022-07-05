<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureMessage;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Webhook\WebhookInterface;
use Mockery;
use Tests\TestCase;

class FlowMeasureMessageTest extends TestCase
{
    private readonly WebhookInterface $webhook;
    private readonly FlowMeasureRecipientsInterface $recipients;
    private readonly FlowMeasureEmbedInterface $embeds;
    private readonly FlowMeasureMessage $message;

    public function setUp(): void
    {
        parent::setUp();
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->recipients = Mockery::mock(FlowMeasureRecipientsInterface::class);
        $this->embeds = Mockery::mock(FlowMeasureEmbedInterface::class);
        $this->message = new FlowMeasureMessage($this->webhook, $this->recipients, $this->embeds);
    }

    public function testItHasADestination()
    {
        $this->assertEquals($this->webhook, $this->message->destination());
    }

    public function testItHasContent()
    {
        $this->recipients->shouldReceive('toString')->andReturn('foo');
        $this->assertEquals('foo', $this->message->content());
    }

    public function testItHasEmbeds()
    {
        $embeds = new EmbedCollection();
        $this->embeds->shouldReceive('embeds')->andReturn($embeds);
        $this->assertEquals($embeds, $this->message->embeds());
    }
}
