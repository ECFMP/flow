<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\AirportGroup;
use App\Enums\FlowMeasureType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Filament\Pages\Actions\Action;
use App\Models\FlightInformationRegion;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FlowMeasureResource;
use App\Helpers\FlowMeasureIdentifierGenerator;

class CreateFlowMeasure extends CreateRecord
{
    protected static string $resource = FlowMeasureResource::class;

    private function hasMultipleAdeps(): bool
    {
        if (
            !in_array(
                Arr::get($this->data, 'type'),
                [
                    FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL->value,
                    FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL->value,
                    FlowMeasureType::PER_HOUR->value,
                    null, // Needed do modal gets initialized
                ]
            )
        ) {
            return false;
        }

        $adeps = Arr::get($this->data, 'adep');

        if (count($adeps) > 1) {
            return true;
        }

        foreach ($adeps as $adep) {
            if (Arr::get($adep, 'value_type') === 'airport_group') {
                return true;
            }

            if (Str::contains(Arr::get($adep, 'custom_value'), '*')) {
                return true;
            }
        }

        return false;
    }

    protected function getFormActions(): array
    {
        if ($this->hasMultipleAdeps()) {
            return array_merge(
                [$this->getCreateWithAdepWarningAction()],
                static::canCreateAnother() ? [$this->getCreateAnotherWithAdepWarningFormAction()] : [],
                [$this->getCancelFormAction()],
            );
        }

        return parent::getFormActions();
    }

    protected function getCreateWithAdepWarningAction(): Action
    {
        return Action::make('create')
            ->label(__('filament::resources/pages/create-record.form.actions.create.label'))
            ->action('create')
            ->keyBindings(['mod+s'])
            ->requiresConfirmation()
            ->modalHeading('WARNING')
            ->modalSubheading(fn () => false)
            ->modalContent(view('filament.resources.flow-measure.modals.multiple-adeps'))
            ->modalCancelAction(fn () => Action::makeModalAction('cancel')
                ->label(__('Return and Edit'))
                ->cancel()
                ->color('primary'))
            ->modalSubmitAction(fn () => Action::makeModalAction('submit')
                ->label(__('Ignore warning, issue flow measure'))
                ->submit('submit')
                ->color('secondary'));
    }

    protected function getCreateAnotherWithAdepWarningFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('filament::resources/pages/create-record.form.actions.create_another.label'))
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('secondary')
            ->requiresConfirmation()
            ->modalHeading('WARNING')
            ->modalSubheading(fn () => false)
            ->modalContent(view('filament.resources.flow-measure.modals.multiple-adeps'))
            ->modalCancelAction(fn () => Action::makeModalAction('cancel')
                ->label(__('Return and Edit'))
                ->cancel()
                ->color('primary'))
            ->modalSubmitAction(fn () => Action::makeModalAction('submit')
                ->label(__('Ignore warning, issue flow measure'))
                ->submit('submit')
                ->color('secondary'));
    }

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
