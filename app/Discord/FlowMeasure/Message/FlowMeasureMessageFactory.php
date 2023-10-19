<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Associator\FlowMeasureAssociator;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsFactory;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedFactory;
use App\Discord\FlowMeasure\Logger\FlowMeasureLogger;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\FlowMeasure\Provider\PendingWebhookMessageInterface;

class FlowMeasureMessageFactory
{
    private readonly FlowMeasureRecipientsFactory $recipientsFactory;
    private readonly FlowMeasureEmbedFactory $embedFactory;

    public function __construct(FlowMeasureRecipientsFactory $recipientsFactory, FlowMeasureEmbedFactory $embedFactory)
    {
        $this->recipientsFactory = $recipientsFactory;
        $this->embedFactory = $embedFactory;
    }

    public function make(PendingWebhookMessageInterface $pendingMessage): FlowMeasureMessage
    {
        return new FlowMeasureMessage(
            $pendingMessage->webhook(),
            $this->recipientsFactory->makeRecipients($pendingMessage),
            $this->embedFactory->make($pendingMessage),
            new FlowMeasureAssociator($pendingMessage->flowMeasure(), $pendingMessage->type()),
            new FlowMeasureLogger($pendingMessage->flowMeasure(), $pendingMessage->type())
        );
    }

    public function makeEcfmp(PendingMessageInterface $message): EcfmpFlowMeasureMessage
    {
        return new EcfmpFlowMeasureMessage(
            config('discord.ecfmp_channel_id'),
            $this->recipientsFactory->makeEcfmpRecipients($message),
            $this->embedFactory->make($message)
        );
    }

}
