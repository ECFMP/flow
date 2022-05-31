<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlowMeasureResource;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlowMeasureController
{
    private readonly FlowMeasureRepository $flowMeasureRepository;

    public function __construct(FlowMeasureRepository $flowMeasureRepository)
    {
        $this->flowMeasureRepository = $flowMeasureRepository;
    }

    public function getFilteredFlowMeasures(Request $request): JsonResource
    {
        /**
         * TODO: Move queries to repository?
         * TODO: Notified only
         * TODO: All pending up to end of active
         * TODO: Cron update
         */

        if ($this->onlyActive($request)) {
            $flowMeasures = $this->flowMeasureRepository->getActiveFlowMeasures($this->includeTrashed($request));
        } else {
            $flowMeasures = $this->flowMeasureRepository->getApiRelevantFlowMeasures($this->includeTrashed($request));
        }

        return FlowMeasureResource::collection($flowMeasures);
    }

    private function includeTrashed(Request $request): bool
    {
        return (int)$request->input('deleted') === 1;
    }

    private function onlyActive(Request $request): bool
    {
        return (int)$request->input('active') === 1;
    }
}
