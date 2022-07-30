<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use App\Filament\Resources\FlowMeasureResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlowMeasures extends ListRecords
{
    protected static string $resource = FlowMeasureResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
