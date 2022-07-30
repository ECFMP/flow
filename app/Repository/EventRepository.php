<?php

namespace App\Repository;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EventRepository
{
    public function getApiRelevantEvents(bool $includeDeleted): Collection
    {
        return $this->upcomingQuery($includeDeleted)
            ->union($this->endTimeWithinOneDayQuery($includeDeleted))
            ->orderBy('id')
            ->get();
    }

    public function getActiveEvents(bool $includeDeleted): Collection
    {
        return $this->activeQuery($includeDeleted)
            ->orderBy('id')
            ->get();
    }

    public function getUpcomingEvents(bool $includeDeleted): Collection
    {
        return $this->upcomingQuery($includeDeleted)
            ->orderBy('id')
            ->get();
    }

    public function getActiveAndUpcomingEvents(bool $includeDeleted): Collection
    {
        return $this->upcomingQuery($includeDeleted)
            ->union($this->activeQuery($includeDeleted))
            ->orderBy('id')
            ->get();
    }

    public function getFinishedEvents(bool $includeDeleted): Collection
    {
        return $this->baseQuery($includeDeleted)
            ->where('date_end', '<', Carbon::now())
            ->orderBy('id')
            ->get();
    }

    private function activeQuery(bool $includeDeleted): Builder
    {
        $now = Carbon::now();

        return $this->baseQuery($includeDeleted)
            ->where('date_start', '<=', $now)
            ->where('date_end', '>', $now);
    }

    private function upcomingQuery(bool $includeDeleted): Builder
    {
        return $this->baseQuery($includeDeleted)
            ->where('date_start', '<', Carbon::now()->addDay())
            ->where('date_start', '>', Carbon::now());
    }

    private function endTimeWithinOneDayQuery(bool $includeDeleted): Builder
    {
        return $this->baseQuery($includeDeleted)
            ->where('date_start', '<', Carbon::now())
            ->where('date_end', '>', Carbon::now()->subDay());
    }

    private function baseQuery(bool $includeDeleted): Builder
    {
        return tap(
            Event::query(),
            function (Builder $builder) use ($includeDeleted) {
                if ($includeDeleted) {
                    $builder->withTrashed();
                }
            }
        );
    }
}
