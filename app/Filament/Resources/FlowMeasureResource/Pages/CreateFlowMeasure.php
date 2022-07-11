<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use App\Enums\FlowMeasureType;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FlowMeasureResource;
use App\Helpers\FlowMeasureIdentifierGenerator;
use App\Models\AirportGroup;
use App\Models\FlightInformationRegion;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreateFlowMeasure extends CreateRecord
{
    protected static string $resource = FlowMeasureResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $fir = FlightInformationRegion::find($data['flight_information_region_id']);

        $startTime = Carbon::parse($data['start_time']);

        if ($startTime->isBefore(now())) {
            $startTime = now();
        }

        $data['identifier'] = FlowMeasureIdentifierGenerator::generateIdentifier($startTime, $fir);
        $data['user_id'] = auth()->id();

        switch ($data['type']) {
            case FlowMeasureType::MANDATORY_ROUTE->value:
                Arr::pull($data, 'value');
                break;
            case FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL->value:
            case FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL->value:
                $data['value'] = $data['seconds'] + ($data['minutes'] * 60);
                break;
        }

        $filters = collect($data['filters'])
            ->groupBy('type')
            ->transform(function (Collection $filter, string $type) {
                if (in_array($type, ['level_above', 'level_below', 'range_to_destination'])) {
                    return collect([
                        'type' => $type,
                        'value' => $filter->pluck('data')->value('value')
                    ]);
                }

                return collect([
                    'type' => $type,
                    'value' => $filter->pluck('data')->pluck('value')
                ]);
            })
            ->values()
            ->add([
                'type' => 'ADEP',
                'value' => $this->getAirportValues($data, 'adep')
            ])
            ->add([
                'type' => 'ADES',
                'value' => $this->getAirportValues($data, 'ades')
            ]);

        $data['filters'] = $filters->toArray();
        Arr::pull($data, 'adep');
        Arr::pull($data, 'ades');

        return $data;
    }

    private function getAirportValues(array $data, string $type): array
    {
        $output = [];
        foreach ($data[$type] as $filterData) {
            if ($filterData['value_type'] == 'airport_group') {
                // Making sure it actually exists
                $airportGroup = AirportGroup::findOrFail($filterData['airport_group'], ['id']);

                $output[] = $airportGroup->getKey();
            } else {
                $output[] = $filterData['custom_value'];
            }
        }

        return $output;
    }
}
