<?php

namespace App\Repository;

use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FlowMeasureRepository
{
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
