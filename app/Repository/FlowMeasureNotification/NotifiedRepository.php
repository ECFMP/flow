<?php

namespace App\Repository\FlowMeasureNotification;

use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotifiedRepository implements RepositoryInterface
{
    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('discordNotifications')
            ->where('start_time', '<', Carbon::now()->addDay())
            ->where('start_time', '>', Carbon::now())
            ->get();
    }
}
