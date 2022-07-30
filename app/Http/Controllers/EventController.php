<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Repository\EventRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventController
{
    private readonly EventRepository $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFilteredEvents(Request $request): JsonResource
    {
        if ($this->activeAndUpcoming($request)) {
            $flowMeasures = $this->repository->getActiveAndUpcomingEvents(
                $this->includeTrashed($request)
            );
        } else {
            if ($this->onlyActive($request)) {
                $flowMeasures = $this->repository->getActiveEvents($this->includeTrashed($request));
            } else if ($this->onlyFinished($request)) {
                $flowMeasures = $this->repository->getFinishedEvents($this->includeTrashed($request));
            } else {
                if ($this->onlyUpcoming($request)) {
                    $flowMeasures = $this->repository->getUpcomingEvents(
                        $this->includeTrashed($request)
                    );
                } else {
                    $flowMeasures = $this->repository->getApiRelevantEvents(
                        $this->includeTrashed($request)
                    );
                }
            }
        }

        return EventResource::collection($flowMeasures);
    }

    private function includeTrashed(Request $request): bool
    {
        return (int)$request->input('deleted') === 1;
    }

    private function onlyActive(Request $request): bool
    {
        return (int)$request->input('active') === 1;
    }

    private function onlyUpcoming(Request $request): bool
    {
        return (int)$request->input('upcoming') === 1;
    }

    private function onlyFinished(Request $request): bool
    {
        return (int)$request->input('finished') === 1;
    }

    private function activeAndUpcoming(Request $request): bool
    {
        return $this->onlyActive($request) && $this->onlyUpcoming($request);
    }
}
