<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Illuminate\Http\JsonResponse;

class PluginApiController
{
    public function __invoke(): JsonResponse
    {
        return response()->json(
            [
                'events' => EventResource::collection(Event::all()),
                'flight_information_regions' => FlightInformationRegionResource::collection(
                    FlightInformationRegion::all()
                ),
                'flow_measures' => FlowMeasureResource::collection(FlowMeasure::all()),
            ]
        );
    }
}
