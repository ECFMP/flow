<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\Message\EcfmpMessageInterface;
use App\Discord\Message\Embed\EmbedCollection;

class EcfmpFlowMeasureMessage implements EcfmpMessageInterface
{
    private readonly string $channel;
    private readonly FlowMeasureRecipientsInterface $recipients;
    private readonly FlowMeasureEmbedInterface $embeds;

    public function __construct(string $channel, FlowMeasureRecipientsInterface $recipients, FlowMeasureEmbedInterface $embeds)
    {
        $this->channel = $channel;
        $this->recipients = $recipients;
        $this->embeds = $embeds;
    }

    public function channel(): string
    {
        return $this->channel;
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
