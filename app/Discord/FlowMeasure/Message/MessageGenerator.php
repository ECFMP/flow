<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Provider\MessageProviderInterface;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use Illuminate\Support\Collection;

class MessageGenerator implements MessageGeneratorInterface
{
    private readonly MessageProviderInterface $messageProvider;
    private readonly FlowMeasureMessageFactory $flowMeasureMessageFactory;

    public function __construct(
        MessageProviderInterface $messageProvider,
        FlowMeasureMessageFactory $flowMeasureMessageFactory
    ) {
        $this->messageProvider = $messageProvider;
        $this->flowMeasureMessageFactory = $flowMeasureMessageFactory;
    }

    public function generate(): Collection
    {
        return $this->messageProvider->pendingMessages()
            ->map(fn(PendingMessageInterface $message) => $this->flowMeasureMessageFactory->make($message));
    }
}
