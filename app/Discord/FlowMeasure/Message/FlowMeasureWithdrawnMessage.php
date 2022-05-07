<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\Message\Content\ContentInterface;
use App\Discord\Message\MessageInterface;

class FlowMeasureWithdrawnMessage implements MessageInterface
{
    private readonly ContentInterface $content;

    public function __construct(ContentInterface $content)
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return sprintf(
            "Flow Measure Withdrawn: \n\n%s",
            $this->content->toString()
        );
    }
}
