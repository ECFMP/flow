<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirportResource\Pages;
use App\Filament\Resources\AirportResource\RelationManagers;
use App\Models\Airport;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class AirportResource extends Resource
{
    protected static ?string $model = Airport::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $recordTitleAttribute = 'icao_code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('icao_code')
                    ->label(__('ICAO code'))
                    ->required()
                    ->unique()
                    ->length(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')
                    ->label(__('ICAO code'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TagsColumn::make('groups.name')
            ])->defaultSort('icao_code')
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\GroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAirports::route('/'),
            'create' => Pages\CreateAirport::route('/create'),
            'view' => Pages\ViewAirport::route('{record}'),
            'edit' => Pages\EditAirport::route('/{record}/edit'),
        ];
    }
}
