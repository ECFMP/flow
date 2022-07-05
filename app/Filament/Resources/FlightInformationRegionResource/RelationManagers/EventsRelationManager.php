<?php

namespace App\Filament\Resources\FlightInformationRegionResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('date_start')
                    ->label('Start (UTC)')
                    ->default(now()->addWeek()->startOfHour())
                    ->withoutSeconds()
                    ->required(),
                Forms\Components\DateTimePicker::make('date_end')
                    ->label('End (UTC)')
                    ->default(now()->addWeek()->addHours(4)->startOfHour())
                    ->withoutSeconds()
                    ->after('date_start')
                    ->required(),
                Forms\Components\TextInput::make('vatcan_code')
                    ->label(__('VATCAN code'))
                    ->helperText(__('Leave empty if no there\'s no code available'))
                    ->maxLength(6),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_start')
                    ->label(__('Start'))
                    ->dateTime('M j, Y H:i\z')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_end')
                    ->label(__('End'))
                    ->dateTime('M j, Y H:i\z')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('vatcan_code')
                    ->label('VATCAN code'),
            ])
            ->defaultSort('date_start')
            ->filters([
                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->default(today()),
                        Forms\Components\DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_end', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_end', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
