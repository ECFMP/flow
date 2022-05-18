<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\MessageInterface;
use App\Models\FlowMeasure;

class FlowMeasureActivatedMessage implements MessageInterface
{
    private readonly FlowMeasure $measure;

    public function __construct(FlowMeasure $measure)
    {
        $this->measure = $measure;
    }

    public function content(): string
    {
        return 'Flow Measure Activated';
    }

    public function embeds(): EmbedCollection
    {
        return (new EmbedCollection())->add();
    }
}
