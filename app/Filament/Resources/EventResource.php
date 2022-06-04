<?php

namespace App\Filament\Resources;

use App\Enums\RoleKey;
use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\FlightInformationRegion;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Event $record */
        return [
            'FIR' => $record->flightInformationRegion->name,
            __('Start') => $record->date_start->format('M j, Y H:i\z'),
            __('End') => $record->date_end->format('M j, Y H:i\z'),
        ];
    }

    private static function setFirOptions(Collection $firs)
    {
        return $firs->mapWithKeys(fn (FlightInformationRegion $fir) => [$fir->id => $fir->identifierName]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('flight_information_region_id')
                    ->label('Flight Information Region')
                    ->hintIcon('heroicon-o-folder')
                    ->searchable()
                    ->options(
                        in_array(auth()->user()->role->key, [
                            RoleKey::SYSTEM,
                            RoleKey::NMT
                        ]) ? self::setFirOptions(FlightInformationRegion::all()) :
                            self::setFirOptions(auth()->user()
                                ->flightInformationRegions)
                    )
                    ->required(),
                Forms\Components\DateTimePicker::make('date_start')
                    ->label('Start (UTC)')
                    ->default(now()->addWeek()->startOfHour())
                    ->withoutSeconds()
                    ->required(),
                Forms\Components\DateTimePicker::make('date_end')
                    ->label('End (UTC)')
                    ->default(now()->addWeek()->addHours(4)->startOfHour())
                    ->withoutSeconds()
                    ->after('date_start')
                    ->required(),
                Forms\Components\TextInput::make('vatcan_code')
                    ->label(__('VATCAN code'))
                    ->helperText(__('Leave empty if no there\'s no code available'))
                    ->maxLength(6),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flightInformationRegion.identifierName')
                    ->label('FIR')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_start')
                    ->label(__('Start'))
                    ->dateTime('M j, Y H:i\z')
                    ->sortable(),
                Tables\Columns\ViewColumn::make('date_end')
                    ->alignCenter()
                    ->view('filament.tables.columns.event.date-end')->sortable(),
                Tables\Columns\BadgeColumn::make('vatcan_code')
                    ->label('VATCAN code'),
            ])
            ->defaultSort('date_start')
            ->filters([
                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->default(today()),
                        Forms\Components\DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_end', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_end', '<=', $date),
                            );
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
