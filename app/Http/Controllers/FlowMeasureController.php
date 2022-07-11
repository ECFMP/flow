<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlowMeasureResource;
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
        if ($this->activeAndNotified($request)) {
            $flowMeasures = $this->flowMeasureRepository->getActiveAndNotifiedFlowMeasures(
                $this->includeTrashed($request)
            );
        } else {
            if ($this->onlyActive($request)) {
                $flowMeasures = $this->flowMeasureRepository->getActiveFlowMeasures($this->includeTrashed($request));
            } else {
                if ($this->onlyNotified($request)) {
                    $flowMeasures = $this->flowMeasureRepository->getNotifiedFlowMeasures(
                        $this->includeTrashed($request)
                    );
                } else {
                    $flowMeasures = $this->flowMeasureRepository->getApiRelevantFlowMeasures(
                        $this->includeTrashed($request)
                    );
                }
            }
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

    private function onlyNotified(Request $request): bool
    {
        return (int)$request->input('notified') === 1;
    }

    private function activeAndNotified(Request $request): bool
    {
        return $this->onlyActive($request) && $this->onlyNotified($request);
    }
}
