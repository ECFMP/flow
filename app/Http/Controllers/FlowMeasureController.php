<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlowMeasureResource;
use App\Models\FlowMeasure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlowMeasureController
{
    public function getFilteredFlowMeasures(Request $request): JsonResource
    {
        /**
         * TODO: Move queries to repository?
         * TODO: Notified only
         * TODO: All pending up to end of active
         * TODO: Cron update
         */
        $query = FlowMeasure::query();
        $unionQuery = FlowMeasure::endTimeWithinOneDay();
        if ($this->includeTrashed($request)) {
            $query->withTrashed();
            $unionQuery->withTrashed();
        }

        if ((int)$request->input('active') === 1) {
            $query->active();
        } else {
            $query->notified()
                ->union($unionQuery);
        }

        return FlowMeasureResource::collection($query->orderBy('id')->get());
    }

    private function includeTrashed(Request $request): bool
    {
        return (int)$request->input('deleted') === 1;
    }
}
