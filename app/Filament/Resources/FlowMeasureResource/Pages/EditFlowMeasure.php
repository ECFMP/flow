<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use Carbon\CarbonInterval;
use Filament\Pages\Actions;
use Illuminate\Support\Arr;
use App\Models\AirportGroup;
use App\Enums\FlowMeasureType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\FlowMeasureResource;
use App\Helpers\FlowMeasureIdentifierGenerator;

class EditFlowMeasure extends EditRecord
{
    protected static string $resource = FlowMeasureResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['edit_mode'] = $this->determineEditMode($data);

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

        if (in_array($data['type'], [FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL,])) {
            // Fill in minutes (if any) and seconds
            $interval = CarbonInterval::seconds($data['value'])->cascade();
            $data['minutes'] = $interval->minutes;
            $data['seconds'] = $interval->seconds;
            $data['value'] = null;
        }

        // Convert to value so we don't have to write extra condition
        $data['type'] = $data['type']->value;

        $newFilters = collect();
        $filters->each(function (array $filter) use ($newFilters) {
            if (in_array($filter['type'], ['level_above', 'level_below', 'range_to_destination'])) {
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($data['edit_mode'] == FlowMeasureResource::FULL_EDIT) {
            $newFlowMeasure = $record->replicate();
            $newFlowMeasure->fill($data);
            $newFlowMeasure->identifier = FlowMeasureIdentifierGenerator::generateIdentifier($newFlowMeasure->start_time, $newFlowMeasure->flightInformationRegion);
            $newFlowMeasure->push();

            $record->delete();

            $this->record = $newFlowMeasure;
            return $newFlowMeasure;
        }

        $record->identifier = FlowMeasureIdentifierGenerator::generateRevisedIdentifier($record);

        $record->update($data);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        if ($this->record->wasRecentlyCreated) {
            return $this->getResource()::getUrl('index');
        }

        return '';
    }

    protected function getSavedNotificationMessage(): ?string
    {
        if ($this->record->wasRecentlyCreated) {
            return __('Flow Measure re-issued');
        }

        return parent::getSavedNotificationMessage();
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

    private function determineEditMode(array $data)
    {
        $startTime = Carbon::parse($data['start_time']);

        if ($startTime->gt(now())) {
            if ($startTime->gt(now()->addMinutes(30))) {
                return FlowMeasureResource::FULL_EDIT;
            }

            return FlowMeasureResource::PARTIAL_EDIT_WITH_START_TIME;
        }

        return FlowMeasureResource::PARTIAL_EDIT;
    }
}
