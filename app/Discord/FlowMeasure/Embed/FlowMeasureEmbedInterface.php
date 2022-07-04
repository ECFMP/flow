<?php

namespace App\Discord\FlowMeasure\Embed;

use App\Discord\Message\Embed\EmbedCollection;

interface FlowMeasureEmbedInterface
{
    public function embeds(): EmbedCollection;
}
