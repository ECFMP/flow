<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
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
    private readonly NotificationReissuerInterface $reissue;

    public function setUp(): void
    {
        parent::setUp();
        $this->measure = FlowMeasure::factory()->create();
        $this->type = DiscordNotificationType::FLOW_MEASURE_ACTIVATED;
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->reissue = Mockery::mock(NotificationReissuerInterface::class);
        $this->message = new PendingDiscordMessage($this->measure, $this->type, $this->webhook, $this->reissue);
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
        $this->assertEquals($this->reissue, $this->message->reissue());
    }
}
