<?php

namespace App\Filament\Resources\FlowMeasureResource\Widgets;

use Closure;
use Filament\Tables;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingFlowMeasures extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return FlowMeasure::query()
            ->where('start_time', '>', now())
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
                ->dateTime('M j, Y H:i\z'),
            Tables\Columns\TextColumn::make('end_time')
                ->dateTime('M j, Y H:i\z'),
        ];
    }
}
