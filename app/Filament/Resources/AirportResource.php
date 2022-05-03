<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Airport;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AirportResource\Pages;
use App\Filament\Resources\AirportResource\RelationManagers;

class AirportResource extends Resource
{
    protected static ?string $model = Airport::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected static ?string $navigationGroup = 'Admin';

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['groups']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Airport $record */
        return [
            __('Groups') => $record->groups->pluck('name')->join(', '),
        ];
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
