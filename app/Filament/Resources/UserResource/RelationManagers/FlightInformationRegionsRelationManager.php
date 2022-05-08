<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;

class FlightInformationRegionsRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'flightInformationRegions';

    protected static ?string $recordTitleAttribute = 'name';

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
                Forms\Components\TextInput::make('identifier')
                    ->required()
                    ->length(4),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifier')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('identifier')
            ->filters([
                //
            ]);
    }
}
