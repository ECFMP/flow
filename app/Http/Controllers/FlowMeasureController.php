<?php

namespace App\Http\Controllers;

use App\Helpers\ApiDateTimeFormatter;
use App\Models\FlowMeasure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\ArrayShape;

class FlowMeasureController
{
    private const CACHE_KEY = 'ACTIVE_FLOW_MEASURES';
    private const CACHE_DURATION_CONFIG_KEY = 'flow-measures.cache_duration_seconds';

    public function getAllFlowMeasures(): JsonResponse
    {
        return response()->json(
            Cache::remember(
                self::CACHE_KEY,
                config(self::CACHE_DURATION_CONFIG_KEY),
                function () {
                    return FlowMeasure::all()
                        ->map(fn(FlowMeasure $flowMeasure) => $this->formatFlowMeasure($flowMeasure));
                }
            )
        );
    }

    public function getFlowMeasure(FlowMeasure $flowMeasure): JsonResponse
    {
        return response()->json($this->formatFlowMeasure($flowMeasure));
    }

    #[ArrayShape([
        'ident' => 'string',
        'group' => 'mixed',
        'reason' => 'mixed',
        'starttime' => 'string',
        'endtime' => 'string',
        'measure' => 'array',
        'filters' => 'array'
    ])] private function formatFlowMeasure(FlowMeasure $flowMeasure): array
    {
        return [
            'ident' => $flowMeasure->identifier,
            'group' => $flowMeasure->event?->name,
            'reason' => $flowMeasure->reason,
            'starttime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->start_time),
            'endtime' => ApiDateTimeFormatter::formatDateTime($flowMeasure->end_time),
            'measure' => [
                'type' => $flowMeasure->type,
                'value' => $flowMeasure->isMandatoryRoute()
                    ? json_encode($flowMeasure->mandatory_route)
                    : $flowMeasure->value,
            ],
            'filters' => $flowMeasure->filters,
        ];
    }
}
