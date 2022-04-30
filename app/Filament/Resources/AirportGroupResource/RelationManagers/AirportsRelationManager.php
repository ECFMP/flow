<?php

namespace App\Filament\Resources\AirportGroupResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class AirportsRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'airports';

    protected static ?string $inverseRelationship = 'groups';

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
                Tables\Columns\TextColumn::make('icao_code')->label(__('ICAO code')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }
}
