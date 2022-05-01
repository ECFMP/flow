<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use Illuminate\Support\Arr;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\FlowMeasureResource;

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
                return ['value' => $value];
            });
        $filters['ades'] = collect($filters['ADES']['value'])
            ->map(function ($value) {
                return ['value' => $value];
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
}
