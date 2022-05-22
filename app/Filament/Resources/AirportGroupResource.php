<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\AirportGroup;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\AirportGroupResource\Pages;
use App\Filament\Resources\AirportGroupResource\RelationManagers;

class AirportGroupResource extends Resource
{
    protected static ?string $model = AirportGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Admin';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var AirportGroup $record */
        return [
            __('Airports') => $record->airport_codes
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TagsColumn::make('airports.icao_code')
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AirportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAirportGroups::route('/'),
            'create' => Pages\CreateAirportGroup::route('/create'),
            'view' => Pages\ViewAirportGroup::route('{record}'),
            'edit' => Pages\EditAirportGroup::route('/{record}/edit'),
        ];
    }
}
