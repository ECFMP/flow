<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingEvents extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Event::query()
            ->where('date_end', '>', now())
            ->orderBy('date_start');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('flightInformationRegion.identifierName')
                ->label(__('Owner')),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('date_start')
                ->dateTime('M j, Y H:i\z'),
            Tables\Columns\ViewColumn::make('date_end')
                ->alignCenter()
                ->view('filament.tables.columns.event.date-end'),
        ];
    }
}
