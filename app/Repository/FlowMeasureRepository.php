<?php

namespace App\Repository;

use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class FlowMeasureRepository
{
    public function getApiRelevantFlowMeasures(bool $includeDeleted): Collection
    {
        $notifiedQuery = FlowMeasure::notified();
        $finishedQuery = FlowMeasure::endTimeWithinOneDay();

        if ($includeDeleted) {
            $notifiedQuery->withTrashed();
            $finishedQuery->withTrashed();
        }

        return $notifiedQuery->union($finishedQuery)->orderBy('id')->get();
    }
}
