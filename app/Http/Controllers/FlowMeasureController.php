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

        return FlowMeasureResource::collection($query->get());
    }

    private function includeTrashed(Request $request): bool
    {
        return (int)$request->input('deleted') === 1;
    }
}
