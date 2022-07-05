<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\MessageInterface;
use App\Discord\Webhook\WebhookInterface;

class FlowMeasureMessage implements MessageInterface
{
    private readonly WebhookInterface $webhook;
    private readonly FlowMeasureRecipientsInterface $recipients;
    private readonly FlowMeasureEmbedInterface $embeds;

    public function __construct(
        WebhookInterface $webhook,
        FlowMeasureRecipientsInterface $recipients,
        FlowMeasureEmbedInterface $embeds
    ) {
        $this->webhook = $webhook;
        $this->recipients = $recipients;
        $this->embeds = $embeds;
    }

    public function destination(): WebhookInterface
    {
        return $this->webhook;
    }

    public function content(): string
    {
        return $this->recipients->toString();
    }

    public function embeds(): EmbedCollection
    {
        return $this->embeds->embeds();
    }
}
