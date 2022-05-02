<?php

namespace App\Filament\Resources\AirportGroupResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;

class AirportsRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'airports';

    protected static ?string $inverseRelationship = 'groups';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    protected function canDeleteAny(): bool
    {
        return false;
    }

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
            ])
            ->filters([
                //
            ]);
    }
}
