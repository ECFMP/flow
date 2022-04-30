<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirportGroupResource\Pages;
use App\Filament\Resources\AirportGroupResource\RelationManagers;
use App\Models\AirportGroup;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class AirportGroupResource extends Resource
{
    protected static ?string $model = AirportGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $recordTitleAttribute = 'name';

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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
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
            'edit' => Pages\EditAirportGroup::route('/{record}/edit'),
        ];
    }
}
