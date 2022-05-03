<?php

namespace App\Filament\Resources\AirportResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;

class GroupsRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'groups';

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
            ])
            ->filters([
                //
            ]);
    }
}
