<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\Message\Content\ContentInterface;
use App\Discord\Message\MessageInterface;

class FlowMeasureExpiredMessage implements MessageInterface
{
    private readonly ContentInterface $content;

    public function __construct(ContentInterface $content)
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return sprintf(
            "Flow Measure Expired: \n\n%s",
            $this->content->toString()
        );
    }
}
