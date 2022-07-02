<?php

namespace App\Repository\FlowMeasureNotification;

use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class ExpiredRepository implements RepositoryInterface
{
    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('discordNotifications')
            ->expiredRecently()
            ->get();
    }
}
