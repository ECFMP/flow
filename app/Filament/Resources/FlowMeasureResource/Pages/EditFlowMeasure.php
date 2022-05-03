<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use App\Filament\Resources\FlowMeasureResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditFlowMeasure extends EditRecord
{
    protected static string $resource = FlowMeasureResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
    }
}
