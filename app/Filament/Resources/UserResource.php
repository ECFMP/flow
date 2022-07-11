<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\RoleKey;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        $roles = collect(Auth::user()->getAssignableRoles())->transform(fn (RoleKey $roleKey) => $roleKey->value);
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('ID')
                    ->disabled()
                    ->dehydrated(false)
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->disabled()
                    ->dehydrated(false)
                    ->required(),
                Forms\Components\BelongsToSelect::make('role_id')
                    ->relationship('role', 'description', fn (Builder $query) => $query->whereIn('key', $roles))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('ID')),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('name'),
                BadgeColumn::make('role.description')
                    ->enum(RoleKey::cases()),
                Tables\Columns\TagsColumn::make('flightInformationRegions.identifierName'),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FlightInformationRegionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
