<?php

namespace App\Filament\Resources;

use App\Enums\FlowMeasureStatus;
use App\Enums\FlowMeasureType;
use App\Enums\RoleKey;
use App\Filament\Resources\FlowMeasureResource\Pages;
use App\Filament\Resources\FlowMeasureResource\Traits\Filters;
use App\Filament\Resources\FlowMeasureResource\Widgets\ActiveFlowMeasures;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\MultiSelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FlowMeasureResource extends Resource
{
    use Filters;

    public const FULL_EDIT = 1;
    public const PARTIAL_EDIT_WITH_START_TIME = 2;
    public const PARTIAL_EDIT = 3;

    protected static ?string $model = FlowMeasure::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    protected static ?string $recordTitleAttribute = 'identifier';

    private static function setFirOptions(Collection $firs)
    {
        return $firs->mapWithKeys(fn (FlightInformationRegion $fir) => [$fir->id => $fir->identifierName]);
    }

    public static function form(Form $form): Form
    {
        $events = Event::whereBetween('date_end', [now()->subHour(6), now()->addDays(10)->addHours(6)])
            ->get(['id', 'name', 'date_start', 'date_end', 'flight_information_region_id'])
            ->keyBy('id');

        return $form
            ->schema([
                Forms\Components\Hidden::make('edit_mode')
                    ->visible(fn (Page $livewire) => $livewire instanceof EditRecord),
                Forms\Components\Select::make('event_id')
                    ->label(__('Event'))
                    ->hintIcon('heroicon-o-calendar')
                    ->searchable()
                    ->options(
                        $events->mapWithKeys(fn (Event $event) => [$event->id => $event->name_date])
                    )
                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) use ($events) {
                        if ($state) {
                            if (!$get('flight_information_region_id')) {
                                $set('flight_information_region_id', $events[$state]->flight_information_region_id);
                            }
                            $set('start_time', $events[$state]->date_start);
                            $set('end_time', $events[$state]->date_end);
                        }
                    })
                    ->disabled(fn (Page $livewire, Closure $get) => !$livewire instanceof CreateRecord && !in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]))
                    ->dehydrated(function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    })
                    ->reactive()
                    ->visible((function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    })),
                Forms\Components\TextInput::make('event_name')
                    ->label(__('Event'))
                    ->hintIcon('heroicon-o-calendar')
                    ->disabled(true)
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, Closure $get) {
                        $component->state(Event::find($get('event_id'))?->name_date ?? null);
                    })
                    ->hidden((function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    })),
                Forms\Components\Select::make('flight_information_region_id')
                    ->label('FIR issuing')
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
                    ->disabled(fn (Page $livewire, Closure $get) => !$livewire instanceof CreateRecord && !in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]))
                    ->dehydrated(function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    })
                    ->visible((function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    }))
                    ->required(),
                Forms\Components\TextInput::make('flight_information_region_name')
                    ->label('FIR issuing')
                    ->hintIcon('heroicon-o-folder')
                    ->disabled(true)
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, Closure $get) {
                        $component->state(FlightInformationRegion::find($get('flight_information_region_id'))?->identifier_name ?? null);
                    })
                    ->hidden((function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    })),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label(__('Start time [UTC]'))
                    ->default(now()->addMinutes(5))
                    ->withoutSeconds()
                    ->minDate(fn (Page $livewire) => $livewire instanceof CreateRecord ? now()->subMinutes(30) : now()->startOfDay())
                    ->maxDate(now()->addDays(10))
                    ->disabled(fn (Page $livewire, Closure $get) => !$livewire instanceof CreateRecord && !in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]))
                    ->dehydrated(function (Page $livewire, Closure $get) {
                        return $livewire instanceof CreateRecord || in_array($get('edit_mode'), [self::FULL_EDIT, self::PARTIAL_EDIT_WITH_START_TIME]);
                    })
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, $state) {
                        $set('end_time', Carbon::parse($state)->addHours(2));
                    })
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label(__('End time [UTC]'))
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
                Forms\Components\Fieldset::make(__('Flow measure'))
                    ->columns(4)->schema([
                        Forms\Components\Select::make('type')
                            ->disabled(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                            ->options(collect(FlowMeasureType::cases())
                                ->mapWithKeys(fn (FlowMeasureType $type) => [$type->value => $type->getFormattedName()]))
                            ->helperText(function (string|FlowMeasureType|null $state) {
                                if (is_a($state, FlowMeasureType::class)) {
                                    return $state->getDescription();
                                }

                                return FlowMeasureType::tryFrom($state)?->getDescription() ?: '';
                            })
                            ->reactive()
                            ->columnSpan(2)
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->columnSpan(2)
                            ->disabled(fn (Closure $get) => in_array($get('type'), [
                                FlowMeasureType::MANDATORY_ROUTE->value,
                                FlowMeasureType::PROHIBIT->value,
                                FlowMeasureType::GROUND_STOP->value,
                            ])
                                || $get('type') == null)
                            ->required(fn (Closure $get) => !in_array($get('type'), [
                                FlowMeasureType::MANDATORY_ROUTE->value,
                                FlowMeasureType::PROHIBIT->value,
                                FlowMeasureType::GROUND_STOP->value
                            ]))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(400)
                            ->step(1)
                            ->hidden(fn (Closure $get) => in_array($get('type'), [
                                FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL->value,
                                FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL->value,
                            ])),
                        Forms\Components\TextInput::make('minutes')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->visible(fn (Closure $get) => in_array($get('type'), [
                                FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL->value,
                                FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL->value,
                            ]))
                            ->required(),
                        Forms\Components\TextInput::make('seconds')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(59)
                            ->visible(fn (Closure $get) => in_array($get('type'), [
                                FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL->value,
                                FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL->value,
                            ]))
                            ->required(),
                        Forms\Components\Repeater::make('mandatory_route')
                            ->columnSpan('full')
                            ->required()
                            ->visible(fn (Closure $get) => $get('type') == FlowMeasureType::MANDATORY_ROUTE->value)
                            ->schema([
                                Forms\Components\Textarea::make('')->required()
                            ]),
                    ]),
                self::filters($events),
                Forms\Components\Fieldset::make('FAO')
                    ->schema([
                        Forms\Components\BelongsToManyMultiSelect::make('notified_flight_information_regions')
                            ->columnSpan('full')
                            ->required()
                            ->helperText(__('The selected FIRs will receive a tag in discord and be visible in the API'))
                            ->label(__("FIR's"))
                            ->relationship('notifiedFlightInformationRegions', 'name')
                            ->getSearchResultsUsing(
                                fn (string $searchQuery) => FlightInformationRegion::where('name', 'like', "%{$searchQuery}%")
                                    ->orWhere('identifier', 'like', "%{$searchQuery}%")
                                    ->orWhereHas('discordTags', fn (Builder $query) => $query->where('tag', 'like', "%{$searchQuery}%")
                                        ->orWhere('description', 'like', "%{$searchQuery}%"))
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn (FlightInformationRegion $fir) => [$fir->id => $fir->identifier_name])
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->identifierName)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifier')
                    ->sortable(),
                Tables\Columns\TextColumn::make('flightInformationRegion.name')
                    ->label(__('Owner'))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->alignCenter()
                    ->colors([
                        'danger',
                        'success' => FlowMeasureStatus::ACTIVE->value,
                        'warning' => FlowMeasureStatus::NOTIFIED->value,
                    ]),
                Tables\Columns\BadgeColumn::make('type')
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => FlowMeasureType::tryFrom($state)->getFormattedName()),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime('M j, Y H:i\z')->sortable(),
                Tables\Columns\ViewColumn::make('end_time')
                    ->alignCenter()
                    ->view('filament.tables.columns.flow-measure.end-time')->sortable(),
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
                MultiSelectFilter::make('flight_information_region_id')
                    ->label(__('FIR'))
                    ->relationship('flightInformationRegion')
                    ->options(fn (): Collection => FlightInformationRegion::orderBy('identifier')
                        ->get(['id', 'name', 'identifier'])
                        ->mapWithKeys(fn (FlightInformationRegion $fir) => [$fir->id => $fir->identifier_name])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'view' => Pages\ViewFlowMeasure::route('/{record}'),
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
