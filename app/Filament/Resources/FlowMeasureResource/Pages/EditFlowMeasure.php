<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use Illuminate\Support\Arr;
use App\Models\AirportGroup;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\FlowMeasureResource;

class EditFlowMeasure extends EditRecord
{
    protected static string $resource = FlowMeasureResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $filters = collect($data['filters'])->keyBy('type');

        $filters['adep'] = collect($filters['ADEP']['value'])
            ->map(function ($value) {
                return $this->buildAirportFilter($value);
            });
        $filters['ades'] = collect($filters['ADES']['value'])
            ->map(function ($value) {
                return $this->buildAirportFilter($value);
            });

        $data['adep'] = $filters['adep']->toArray();
        $data['ades'] = $filters['ades']->toArray();

        $filters->pull('adep');
        $filters->pull('ades');
        $filters->pull('ADEP');
        $filters->pull('ADES');

        $filters =  $filters->map(function (array $filter) {
            $filter['data'] = ['value' => $filter['value']];
            Arr::pull($filter, 'value');

            return $filter;
        });

        $data['filters'] = $filters->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $filters = collect($data['filters'])->map(function (array $filter) {
            $filter['value'] = $filter['data']['value'];
            Arr::pull($filter, 'data');

            return $filter;
        });

        $filters->add([
            'type' => 'ADEP',
            'value' => $this->getAirportValues($data, 'adep')
        ])->add([
            'type' => 'ADES',
            'value' => $this->getAirportValues($data, 'ades')
        ]);

        $data['filters'] = $filters->toArray();
        Arr::pull($data, 'adep');
        Arr::pull($data, 'ades');

        return $data;
    }

    private function buildAirportFilter(string $value): array
    {
        if (AirportGroup::find($value)) {
            return [
                'value_type' => 'airport_group',
                'airport_group' => $value,
                'custom_value' => '',
            ];
        }

        return [
            'value_type' => 'custom_value',
            'airport_group' => null,
            'custom_value' => $value,
        ];
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
