<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsFactory;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedFactory;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;

class FlowMeasureMessageFactory
{
    private readonly FlowMeasureRecipientsFactory $recipientsFactory;
    private readonly FlowMeasureEmbedFactory $embedFactory;

    public function __construct(FlowMeasureRecipientsFactory $recipientsFactory, FlowMeasureEmbedFactory $embedFactory)
    {
        $this->recipientsFactory = $recipientsFactory;
        $this->embedFactory = $embedFactory;
    }

    public function make(PendingMessageInterface $pendingMessage): FlowMeasureMessage
    {
        return new FlowMeasureMessage(
            $pendingMessage->webhook(),
            $this->recipientsFactory->makeRecipients($pendingMessage),
            $this->embedFactory->make($pendingMessage)
        );
    }
}
