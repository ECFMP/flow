<?php

namespace Tests\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\FlowMeasure\Provider\PendingDiscordWebhookMessage;
use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Mockery;
use Tests\TestCase;

class PendingDiscordWebhookMessageTest extends TestCase
{
    private readonly FlowMeasure $measure;
    private readonly DiscordNotificationType $type;
    private readonly WebhookInterface $webhook;
    private readonly PendingDiscordWebhookMessage $message;
    private readonly NotificationReissuerInterface $reissue;

    public function setUp(): void
    {
        parent::setUp();
        $this->measure = FlowMeasure::factory()->create();
        $this->type = DiscordNotificationType::FLOW_MEASURE_ACTIVATED;
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->reissue = Mockery::mock(NotificationReissuerInterface::class);
        $this->message = new PendingDiscordWebhookMessage($this->measure, $this->type, $this->webhook, $this->reissue);
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

    public function testItIsNotEcfmp()
    {
        $this->assertFalse($this->message->isEcfmp());
    }
}
