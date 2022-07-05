<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\Message\Emoji\Emoji;
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
                Arr::join($this->tags->toArray(), ' '),
                Emoji::WHITE_CHECK_MARK->value
            );
    }
}
