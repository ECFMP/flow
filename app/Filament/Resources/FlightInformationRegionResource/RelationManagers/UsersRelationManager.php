<?php

namespace App\Filament\Resources\FlightInformationRegionResource\RelationManagers;

use App\Enums\RoleKey;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;

class UsersRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                BadgeColumn::make('role.key')
                    ->enum(RoleKey::cases())
            ])
            ->filters([
                //
            ]);
    }
}
