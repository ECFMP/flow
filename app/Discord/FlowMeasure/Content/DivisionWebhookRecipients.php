<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\Message\Tag\TagInterface;
use Illuminate\Support\Collection;

class DivisionWebhookRecipients implements FlowMeasureRecipientsInterface
{
    private readonly Collection $tags;

    public function __construct(Collection $tags)
    {
        $this->tags = $tags;
    }

    public function toString(): string
    {
        return $this->tags
            ->map(fn(TagInterface $tag) => (string)$tag)
            ->join(' ');
    }
}
