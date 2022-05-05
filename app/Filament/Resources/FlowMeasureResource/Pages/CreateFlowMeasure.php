<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use App\Enums\FlowMeasureType;
use App\Events\FlowMeasureCreatedEvent;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FlowMeasureResource;
use App\Helpers\FlowMeasureIdentifierGenerator;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreateFlowMeasure extends CreateRecord
{
    protected static string $resource = FlowMeasureResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!$data['event_id']) {
            $fir = FlightInformationRegion::find($data['flight_information_region_id']);
        } else {
            $fir = Event::find($data['event_id'])->flightInformationRegion;
            $data['flight_information_region_id'] ??= $fir->id;
        }

        $startTime = Carbon::parse($data['start_time']);
        $data['identifier'] = FlowMeasureIdentifierGenerator::generateIdentifier($startTime, $fir);
        $data['user_id'] = auth()->id();

        if ($data['type'] == FlowMeasureType::MANDATORY_ROUTE) {
            Arr::pull($data, 'value');
        }

        $filters = collect($data['filters'])->map(function (array $filter) {
            $filter['value'] = $filter['data']['value'];
            Arr::pull($filter, 'data');

            return $filter;
        });

        $filters->add([
            'type' => 'ADEP',
            'value' => Arr::pluck($data['adep'], 'value'),
        ])->add([
            'type' => 'ADES',
            'value' => Arr::pluck($data['ades'], 'value'),
        ]);

        $data['filters'] = $filters->toArray();
        Arr::pull($data, 'adep');
        Arr::pull($data, 'ades');

        return $data;
    }#

    protected function afterCreate()
    {
        event(new FlowMeasureCreatedEvent($this->record));
    }
}
