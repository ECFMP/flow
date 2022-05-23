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
        if ((int) $request->input('active') === 1) {
            $query->active();
        } else if ((int) $request->input('deleted') === 1) {
            $query->withTrashed();
        }

        return FlowMeasureResource::collection($query->get());
    }
}
