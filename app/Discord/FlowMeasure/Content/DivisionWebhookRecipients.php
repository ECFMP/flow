<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\Message\Tag\TagInterface;

class DivisionWebhookRecipients implements FlowMeasureRecipientsInterface
{
    private readonly TagInterface $tag;

    public function __construct(TagInterface $tag)
    {
        $this->tag = $tag;
    }

    public function toString(): string
    {
        return $this->tag;
    }
}
