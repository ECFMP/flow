<?php

namespace App\Http\Resources;

use App\Helpers\ApiDateTimeFormatter;
use App\Helpers\FlowMeasureFilterApiFormatter;
use App\Models\Event;
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
            'withdrawn_at' => $this->deleted_at ? ApiDateTimeFormatter::formatDateTime($this->deleted_at) : null,
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

    private function formatSingleFilter(string $type, $value): array |int|string
    {
        return match ($type) {
            'ADES', 'ADEP' => FlowMeasureFilterApiFormatter::formatAirportList($value),
            'level_above', 'level_below' => (int) $value,
            'level' => array_map(fn ($level) => (int) $level, $value),
            'member_event', 'member_not_event' => $this->formatEventMembershipFilters($value),
            default => $value
        };
    }

    private function formatEventMembershipFilters($value): array
    {
        $event = Event::withTrashed()->where('id', $value)->first();

        return [
            'event_id' => $event->id,
            'event_api' => null,
            'event_vatcan' => $event->vatcan_code,
        ];
    }
}
