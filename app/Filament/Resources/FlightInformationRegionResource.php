<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use App\Models\FlightInformationRegion;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\FlightInformationRegionResource\Pages;
use App\Filament\Resources\FlightInformationRegionResource\RelationManagers;

class FlightInformationRegionResource extends Resource
{
    protected static ?string $model = FlightInformationRegion::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Admin';

    // TODO: Do we want to show 'Flight Information Region' or 'FIR'?
    // protected static ?string $label = 'FIR';
    // protected static ?string $pluralLabel = 'FIR\'s';

    public static function getGloballySearchableAttributes(): array
    {
        return ['identifier', 'name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Identifier') => $record->identifier,
        ];
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlightInformationRegions::route('/'),
            'create' => Pages\CreateFlightInformationRegion::route('/create'),
            'view' => Pages\ViewFlightInformationRegion::route('{record}'),
            'edit' => Pages\EditFlightInformationRegion::route('/{record}/edit'),
        ];
    }
}
