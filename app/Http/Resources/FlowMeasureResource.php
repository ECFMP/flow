<?php

namespace App\Http\Resources;

use App\Helpers\ApiDateTimeFormatter;
use App\Helpers\FlowMeasureFilterApiFormatter;
use Illuminate\Http\Resources\Json\JsonResource;

class FlowMeasureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ident' => $this->identifier,
            'event_id' => $this->event?->id,
            'reason' => $this->reason,
            'starttime' => ApiDateTimeFormatter::formatDateTime($this->start_time),
            'endtime' => ApiDateTimeFormatter::formatDateTime($this->end_time),
            'measure' => [
                'type' => $this->type,
                'value' => $this->isMandatoryRoute()
                    ? $this->mandatory_route
                    : $this->value,
            ],
            'filters' => $this->formatFilters($this->filters),
            'notified_flight_information_regions' => $this->notifiedFlightInformationRegions->pluck('id')->toArray(),
        ];
    }

    private function formatFilters(array $filters): array
    {
        return array_map(
            fn (array $filter) => [
                'type' => $filter['type'],
                'value' => $this->formatSingleFilter($filter['type'], $filter['value']),
            ],
            $filters
        );
    }

    private function formatSingleFilter(string $type, $value): array|int|string
    {
        return match ($type) {
            'ADES', 'ADEP' => FlowMeasureFilterApiFormatter::formatAirportList($value),
            'level_above', 'level_below' => (int) $value,
            'level' => array_map(fn ($level) => (int) $level, $value),
            'member_event', 'member_not_event' => [
                'event_id' => (int) $value['event_id'],
                'event_api' => $value['event_api'],
                'event_vatcan' => $value['event_vatcan'],
            ],
            default => $value
        };
    }
}
