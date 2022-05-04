<?php

namespace App\Filament\Resources\FlowMeasureResource\Widgets;

use App\Models\FlowMeasure;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

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
            Tables\Columns\TextColumn::make('user.name')
                ->label(__('Owner')),
            Tables\Columns\BadgeColumn::make('type'),
            Tables\Columns\TextColumn::make('value'),
            Tables\Columns\TextColumn::make('start_time')
                ->dateTime('H:i\z'),
            Tables\Columns\TextColumn::make('end_time')
                ->dateTime('H:i\z'),
        ];
    }
}
