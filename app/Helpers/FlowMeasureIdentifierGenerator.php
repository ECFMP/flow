<?php

namespace App\Helpers;

use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FlowMeasureIdentifierGenerator
{
    private const IDENTIFIER_REGEX = '/^([A-Z]{4})(\d{2})([A-Z]{1,2})(-(\d+))?$/';

    public static function generateRevisedIdentifier(FlowMeasure $measure): string
    {
        $identifierParts = [];
        preg_match(self::IDENTIFIER_REGEX, $measure->identifier, $identifierParts);

        return sprintf(
            '%s%s%s-%d',
            $identifierParts[1],
            $identifierParts[2],
            $identifierParts[3],
            isset($identifierParts[5]) ? ((int)$identifierParts[5]) + 1 : 2
        );
    }

    public static function generateIdentifier(
        Carbon                  $startTime,
        FlightInformationRegion $flightInformationRegion
    ): string
    {
        return sprintf(
            '%s%s%s',
            $flightInformationRegion->identifier,
            Str::padLeft($startTime->day, 2, '0'),
            self::designator($startTime, $flightInformationRegion)
        );
    }

    public static function timesRevised(FlowMeasure $flowMeasure)
    {
        $identifierParts = [];
        preg_match(self::IDENTIFIER_REGEX, $flowMeasure->identifier, $identifierParts);

        return isset($identifierParts[5]) ? ((int)$identifierParts[5]) - 1 : 0;
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
