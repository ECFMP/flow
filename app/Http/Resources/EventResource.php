<?php

namespace App\Http\Resources;

use App\Helpers\ApiDateTimeFormatter;
use App\Models\EventParticipant;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'date_start' => ApiDateTimeFormatter::formatDateTime($this->date_start),
            'date_end' => ApiDateTimeFormatter::formatDateTime($this->date_end),
            'flight_information_region_id' => $this->flight_information_region_id,
            'vatcan_code' => $this->vatcan_code,
            'participants' => $this->participants->map(fn (EventParticipant $eventParticipant) => [
                'cid' => $eventParticipant->cid,
                'destination' => $eventParticipant->destination,
                'origin' => $eventParticipant->origin,
            ]),
        ];
    }
}
