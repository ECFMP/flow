<?php

namespace App\Filament\Resources\FlowMeasureResource\Pages;

use App\Filament\Resources\FlowMeasureResource;
use Closure;
use Filament\Resources\Pages\EditRecord;

class EditFlowMeasure extends EditRecord
{
    protected static string $resource = FlowMeasureResource::class;

    protected function beforeFill(): void
    {
        // dd($this->record);
    }
}
