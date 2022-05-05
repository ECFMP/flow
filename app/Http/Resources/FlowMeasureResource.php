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
            function (array $filter) {
                if ($filter['type'] === 'ADES' || $filter['type'] === 'ADEP') {
                    return [
                        'type' => $filter['type'],
                        'value' => FlowMeasureFilterApiFormatter::formatAirportList($filter['value']),
                    ];
                }

                return $filter;
            },
            $filters
        );
    }
}
