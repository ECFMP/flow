<?php

namespace App\Discord\FlowMeasure\Webhook;

use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

interface MapperInterface
{
    public function mapToWebhooks(FlowMeasure $measure): Collection;
}
