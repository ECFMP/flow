<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use App\Enums\RoleKey;
use Filament\Pages\Page;
use App\Models\FlowMeasure;
use App\Models\AirportGroup;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Enums\FlowMeasureType;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use App\Models\FlightInformationRegion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Builder\Block;
use App\Filament\Resources\FlowMeasureResource\Pages;
use App\Filament\Resources\FlowMeasureResource\RelationManagers;
use App\Filament\Resources\FlowMeasureResource\Widgets\ActiveFlowMeasures;

class FlowMeasureResource extends Resource
{
    protected static ?string $model = FlowMeasure::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    protected static ?string $recordTitleAttribute = 'identifier';

    private static function setFirOptions(Collection $firs)
    {
        return $firs->mapWithKeys(fn (FlightInformationRegion $fir) => [$fir->id => $fir->identifierName]);
    }

    public static function form(Form $form): Form
    {
        $events = Event::where('date_end', '>', now()->addHours(6))
            ->get(['id', 'name', 'date_start', 'date_end', 'flight_information_region_id'])
            ->keyBy('id');

        return $form
            ->schema([
                Forms\Components\Select::make('flight_information_region_id')
                    ->label('Flight Information Region')
                    ->helperText(__('Required if event is left empty'))
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
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->required(fn (Closure $get) => $get('event_id') == null),
                Forms\Components\Select::make('event_id')
                    ->label(__('Event'))
                    ->hintIcon('heroicon-o-calendar')
                    ->searchable()
                    ->options(
                        $events->mapWithKeys(fn (Event $event) => [$event->id => $event->name_date])
                    )
                    ->afterStateUpdated(function (Closure $set, $state) use ($events) {
                        if ($state) {
                            $set('flight_information_region_id', $events[$state]->flight_information_region_id);
                            $set('start_time', $events[$state]->date_start);
                            $set('end_time', $events[$state]->date_end);
                        }
                    })
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->reactive()
                    ->required(fn (Closure $get) => $get('flight_information_region_id') == null),
                Forms\Components\DateTimePicker::make('start_time')
                    ->default(now()->addMinutes(5))
                    ->withoutSeconds()
                    ->afterOrEqual(now())
                    ->minDate(now())
                    ->maxDate(now()->addDays(10))
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, $state) {
                        $set('end_time', Carbon::parse($state)->addHours(2));
                    })
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->default(now()->addHours(2)->addMinutes(5))
                    ->withoutSeconds()
                    ->after('start_time')
                    ->minDate(now())
                    ->maxDate(now()->addDays(10))
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->columnSpan('full')
                    ->maxLength(65535),
                Forms\Components\Fieldset::make(__('Flow measure'))->schema([
                    Forms\Components\Select::make('type')
                        ->options(collect(FlowMeasureType::cases())
                            ->mapWithKeys(fn (FlowMeasureType $type) => [$type->value => $type->getFormattedName()]))
                        ->helperText(function (string|FlowMeasureType|null $state) {
                            if (is_a($state, FlowMeasureType::class)) {
                                return $state->getDescription();
                            }

                            return FlowMeasureType::tryFrom($state)?->getDescription() ?: '';
                        })
                        ->reactive()
                        ->required(),
                    Forms\Components\TextInput::make('value')
                        ->disabled(fn (Closure $get) => in_array($get('type'), [
                            FlowMeasureType::MANDATORY_ROUTE->value,
                            FlowMeasureType::PROHIBIT->value,
                        ]) || $get('type') == null)
                        ->required(fn (Closure $get) => !in_array($get('type'), [
                            FlowMeasureType::MANDATORY_ROUTE->value,
                            FlowMeasureType::PROHIBIT->value,
                        ])),
                    Forms\Components\Repeater::make('mandatory_route')
                        ->columnSpan('full')
                        ->required()
                        ->visible(fn (Closure $get) => $get('type') == FlowMeasureType::MANDATORY_ROUTE->value)
                        ->schema([
                            Forms\Components\Textarea::make('')->required()
                        ]),
                ]),
                Forms\Components\Fieldset::make(__('Filters'))->schema([

                    Forms\Components\Repeater::make('adep')
                        ->label('ADEP')
                        ->required()
                        ->label('Departure(s) [ADEP]')
                        ->schema([
                            Forms\Components\Select::make('value_type')
                                ->options([
                                    'airport_group' => __('Airport Group'),
                                    'custom_value' => __('Custom value'),
                                ])
                                ->reactive()
                                ->required(),
                            Forms\Components\Select::make('airport_group')
                                ->visible(fn (Closure $get) => $get('value_type') == 'airport_group')
                                ->searchable()
                                ->options(AirportGroup::all()->pluck('name_codes', 'id'))
                                ->required(),
                            Forms\Components\TextInput::make('custom_value')
                                ->visible(fn (Closure $get) => $get('value_type') == 'custom_value')
                                ->length(4)
                                ->default('****')
                                ->required()
                        ]),
                    Forms\Components\Repeater::make('ades')
                        ->label('ADES')
                        ->required()
                        ->label('Arrival(s) [ADES]')
                        ->schema([
                            Forms\Components\Select::make('value_type')
                                ->options([
                                    'airport_group' => __('Airport Group'),
                                    'custom_value' => __('Custom value'),
                                ])
                                ->reactive()
                                ->required(),
                            Forms\Components\Select::make('airport_group')
                                ->visible(fn (Closure $get) => $get('value_type') == 'airport_group')
                                ->searchable()
                                ->options(AirportGroup::all()->pluck('name_codes', 'id'))
                                ->required(),
                            Forms\Components\TextInput::make('custom_value')
                                ->visible(fn (Closure $get) => $get('value_type') == 'custom_value')
                                ->length(4)
                                ->default('****')
                                ->required()
                        ]),
                    Forms\Components\Builder::make('filters')
                        ->label('Optional filters')
                        ->createItemButtonLabel(__('Add optional filter'))
                        ->columnSpan('full')
                        ->blocks([
                            Block::make('waypoint')
                                ->schema([
                                    Forms\Components\Textarea::make('value')
                                        ->label(__('Waypoint'))
                                        ->required()
                                ]),
                            Block::make('level_above')
                                ->schema([
                                    // TODO: Add mask?
                                    Forms\Components\TextInput::make('value')
                                        ->label(__('Level above'))
                                        ->numeric()
                                        ->step(5)
                                        ->prefix('FL')
                                        ->minLength(0)
                                        ->maxLength(660)
                                        ->required()
                                ]),
                            Block::make('level_below')
                                ->schema([
                                    // TODO: Add mask?
                                    Forms\Components\TextInput::make('value')
                                        ->label(__('Level below'))
                                        ->numeric()
                                        ->step(5)
                                        ->prefix('FL')
                                        ->minLength(0)
                                        ->maxLength(660)
                                        ->required()
                                ]),
                            Block::make('level')
                                ->schema([
                                    // TODO: Add mask?
                                    Forms\Components\TextInput::make('value')
                                        ->label(__('Level'))
                                        ->numeric()
                                        ->step(5)
                                        ->prefix('FL')
                                        ->minLength(0)
                                        ->maxLength(660)
                                        ->required()
                                ]),
                            Block::make('member_event')
                                ->schema([
                                    Forms\Components\Select::make('member_event')
                                        ->label(__('Event'))
                                        ->hintIcon('heroicon-o-calendar')
                                        ->searchable()
                                        ->options(
                                            $events->mapWithKeys(fn (Event $event) => [$event->id => $event->name_date])
                                        )
                                ]),
                            Block::make('member_non_event')
                                ->schema([
                                    Forms\Components\Select::make('member_non_event')
                                        ->label(__('Event'))
                                        ->hintIcon('heroicon-o-calendar')
                                        ->searchable()
                                        ->options(
                                            $events->mapWithKeys(fn (Event $event) => [$event->id => $event->name_date])
                                        )
                                ]),
                        ]),
                ]),
                // TODO: Make it possible to also search by identifier
                Forms\Components\Fieldset::make('FAO')
                    ->schema([
                        Forms\Components\BelongsToManyMultiSelect::make('notified_flight_information_regions')
                            ->columnSpan('full')
                            ->label(__("FIR's"))
                            ->relationship('notifiedFlightInformationRegions', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->identifierName)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifier')->sortable(),
                Tables\Columns\TextColumn::make('flightInformationRegion.identifierName')
                    ->label(__('Owner')),
                Tables\Columns\BadgeColumn::make('type')
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => FlowMeasureType::tryFrom($state)->getFormattedName()),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime('M j, Y H:i\z')->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime('M j, Y H:i\z')->sortable(),
            ])
            ->defaultSort('start_time')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('end_time', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_time', '<=', $date),
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
            'index' => Pages\ListFlowMeasures::route('/'),
            'create' => Pages\CreateFlowMeasure::route('/create'),
            'view' => Pages\ViewFlowMeasure::route('{record}'),
            'edit' => Pages\EditFlowMeasure::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ActiveFlowMeasures::class,
        ];
    }
}
