<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Associator\FlowMeasureAssociator;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\FlowMeasure\Logger\FlowMeasureLogger;
use App\Discord\Message\Associator\AssociatorInterface;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Logger\LoggerInterface;
use App\Discord\Message\MessageInterface;
use App\Discord\Webhook\WebhookInterface;

class FlowMeasureMessage implements MessageInterface
{
    private readonly WebhookInterface $webhook;
    private readonly FlowMeasureRecipientsInterface $recipients;
    private readonly FlowMeasureEmbedInterface $embeds;
    private readonly FlowMeasureAssociator $associator;
    private readonly FlowMeasureLogger $logger;

    public function __construct(
        WebhookInterface $webhook,
        FlowMeasureRecipientsInterface $recipients,
        FlowMeasureEmbedInterface $embeds,
        FlowMeasureAssociator $associator,
        FlowMeasureLogger $logger
    ) {
        $this->webhook = $webhook;
        $this->recipients = $recipients;
        $this->embeds = $embeds;
        $this->associator = $associator;
        $this->logger = $logger;
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

    public function associator(): AssociatorInterface
    {
        return $this->associator;
    }

    public function logger(): LoggerInterface
    {
        return $this->logger;
    }
}
