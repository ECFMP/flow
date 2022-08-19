<?php

namespace App\Repository;

use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FlowMeasureRepository
{
    public function getFlowMeasuresActiveDuringPeriod(Carbon $start, Carbon $end): Collection
    {
        return $this->baseQuery(false)
            ->startsBetween($start, $end)
            ->union($this->baseQuery(false)->endsBetween($start, $end))
            ->union($this->baseQuery(false)->activeThroughout($start, $end))
            ->orderBy('id')
            ->get();
    }

    public function getApiRelevantFlowMeasures(bool $includeDeleted): Collection
    {
        return $this->baseQuery($includeDeleted)
            ->notified()
            ->union($this->baseQuery($includeDeleted)->endTimeWithinOneDay())
            ->orderBy('id')
            ->get();
    }

    public function getActiveFlowMeasures(bool $includeDeleted): Collection
    {
        return $this->baseQuery($includeDeleted)
            ->active()
            ->orderBy('id')
            ->get();
    }

    public function getNotifiedFlowMeasures(bool $includeDeleted): Collection
    {
        return $this->baseQuery($includeDeleted)
            ->notified()
            ->orderBy('id')
            ->get();
    }

    public function getActiveAndNotifiedFlowMeasures(bool $includeDeleted): Collection
    {
        return $this->baseQuery($includeDeleted)
            ->notified()
            ->union($this->baseQuery($includeDeleted)->active())
            ->orderBy('id')
            ->get();
    }

    private function baseQuery(bool $includeDeleted): Builder
    {
        return tap(
            FlowMeasure::query(),
            function (Builder $builder) use ($includeDeleted) {
                if ($includeDeleted) {
                    $builder->withTrashed();
                }
            }
        );
    }
}
