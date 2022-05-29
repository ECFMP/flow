<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use Illuminate\Support\Arr;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\FlowMeasureResource;
use App\Models\AirportGroup;

class ViewFlowMeasure extends ViewRecord
{
    protected static string $resource = FlowMeasureResource::class;

    protected function fillForm(): void
    {
        // Copied from EditRecord, I need that logic from murateFormDataBeforeFill
        $this->callHook('beforeFill');

        $data = $this->record->toArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

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

        $newFilters = collect();
        $filters->each(function (array $filter) use ($newFilters) {
            if (in_array($filter['type'], ['level_above', 'level_below'])) {
                $newFilters->push([
                    'type' => $filter['type'],
                    'value' => $filter['value'],
                ]);
            } else {
                foreach ($filter['value'] as $value) {
                    $newFilters->push([
                        'type' => $filter['type'],
                        'value' => $value
                    ]);
                }
            }
        });

        $newFilters = $newFilters->map(function (array $filter) {
            $filter['data'] = ['value' => $filter['value']];
            Arr::pull($filter, 'value');

            return $filter;
        });

        $data['filters'] = $newFilters->toArray();

        return $data;
    }

    private function buildAirportFilter(string $value): array
    {
        $airportGroup = AirportGroup::find($value);
        if ($airportGroup) {
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
}
