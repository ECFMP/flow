<?php

namespace App\Filament\Resources\FlightInformationRegionResource\RelationManagers;

use App\Enums\RoleKey;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    protected function canDeleteAny(): bool
    {
        return false;
    }

    protected function canEdit(Model $record): bool
    {
        // TODO: Might add role stuff here
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                BadgeColumn::make('role.description')
                    ->enum(RoleKey::cases())
            ])
            ->filters([
                //
            ]);
    }
}
