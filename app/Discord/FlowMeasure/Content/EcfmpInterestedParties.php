<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\Message\Emoji\Emoji;
use App\Discord\Message\Tag\TagInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EcfmpInterestedParties implements FlowMeasureRecipientsInterface
{
    private readonly Collection $tags;

    public function __construct(Collection $tags)
    {
        $this->tags = $tags;
    }

    public function toString(): string
    {
        return $this->tags->isEmpty()
            ? ''
            : sprintf(
                "**FAO**: %s\nPlease acknowledge receipt with a %s reaction.",
                Arr::join(
                    $this->tags->unique(fn (TagInterface $tag) => (string)$tag)->toArray(),
                    ' '
                ),
                Emoji::WHITE_CHECK_MARK->value
            );
    }
}
