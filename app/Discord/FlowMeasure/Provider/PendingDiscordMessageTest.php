<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Mockery;
use Tests\TestCase;

class PendingDiscordMessageTest extends TestCase
{
    private readonly FlowMeasure $measure;
    private readonly DiscordNotificationType $type;
    private readonly WebhookInterface $webhook;
    private readonly PendingDiscordMessage $message;

    public function setUp(): void
    {
        parent::setUp();
        $this->measure = FlowMeasure::factory()->create();
        $this->type = DiscordNotificationType::FLOW_MEASURE_ACTIVATED;
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->message = new PendingDiscordMessage($this->measure, $this->type, $this->webhook, true);
    }

    public function testItHasAMeasure()
    {
        $this->assertEquals($this->measure, $this->message->flowMeasure());
    }

    public function testItHasAType()
    {
        $this->assertEquals($this->type, $this->message->type());
    }

    public function testItHasAWebhook()
    {
        $this->assertEquals($this->webhook, $this->message->webhook());
    }

    public function testItIsReissued()
    {
        $this->assertTrue($this->message->reissue());
    }
}
