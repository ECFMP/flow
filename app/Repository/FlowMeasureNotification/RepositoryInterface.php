<?php

namespace App\Repository\FlowMeasureNotification;

use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * Get all the flow measures for notification.
     */
    public function flowMeasuresForNotification(): Collection;
}
