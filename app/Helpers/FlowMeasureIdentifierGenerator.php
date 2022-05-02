<?php

namespace App\Helpers;

use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FlowMeasureIdentifierGenerator
{
    public static function generateIdentifier(
        Carbon $startTime,
        FlightInformationRegion $flightInformationRegion
    ): string {
        return sprintf(
            '%s%s%s',
            $flightInformationRegion->identifier,
            Str::padLeft($startTime->day, 2, '0'),
            self::designator($startTime, $flightInformationRegion)
        );
    }

    private static function designator(Carbon $startTime, FlightInformationRegion $flightInformationRegion): string
    {
        $flowMeasuresToday = FlowMeasure::where('start_time', '>=', $startTime->copy()->startOfDay())
            ->where('start_time', '<=', $startTime->copy()->endOfDay())
            ->flightInformationRegion($flightInformationRegion)
            ->withTrashed()
            ->count();

        $multiples = (int)($flowMeasuresToday / 26);
        return sprintf('%s%s', $multiples === 0 ? '' : chr(64 + $multiples), chr(65 + ($flowMeasuresToday % 26)));
    }
}
