<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Repository\FlowMeasureRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginApiController
{
    private readonly FlowMeasureRepository $flowMeasureRepository;

    public function __construct(FlowMeasureRepository $flowMeasureRepository)
    {
        $this->flowMeasureRepository = $flowMeasureRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            [
                'events' => EventResource::collection(Event::all()),
                'flight_information_regions' => FlightInformationRegionResource::collection(
                    FlightInformationRegion::all()
                ),
                'flow_measures' => FlowMeasureResource::collection(
                    $this->flowMeasureRepository->getApiRelevantFlowMeasures($request->query('deleted', '0') === '1')
                ),
            ]
        );
    }
}
