<?php

namespace App\Filament\Resources\FlowMeasureResource\Widgets;

use Filament\Tables;
use App\Models\FlowMeasure;
use App\Enums\FlowMeasureType;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveFlowMeasures extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return FlowMeasure::query()
            ->active()
            ->orderBy('start_time');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('identifier'),
            Tables\Columns\TextColumn::make('flightInformationRegion.identifierName')
                ->label(__('Owner')),
            Tables\Columns\BadgeColumn::make('type')
                ->alignCenter()
                ->formatStateUsing(fn (string $state): string => FlowMeasureType::tryFrom($state)->getFormattedName()),
            Tables\Columns\TextColumn::make('value'),
            Tables\Columns\ViewColumn::make('end_time')
                ->alignCenter()
                ->view('filament.tables.columns.flow-measure.end-time'),
        ];
    }
}
