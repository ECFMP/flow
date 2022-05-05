<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\Message\Content\ContentInterface;
use App\Discord\Message\MessageInterface;

class FlowMeasureActivatedMessage implements MessageInterface
{
    private readonly ContentInterface $content;

    public function __construct(ContentInterface $content)
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return sprintf(
            "Flow Measure Activated: \n\n%s",
            $this->content->toString()
        );
    }
}
