<?php

namespace App\Models;

use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Enums\FilterType;
use App\Enums\FlowMeasureStatus;
use App\Enums\FlowMeasureType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlowMeasure extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'identifier',
        'canonical_identifier',
        'revision_number',
        'user_id',
        'flight_information_region_id',
        'event_id',
        'reason',
        'type',
        'value',
        'mandatory_route',
        'filters',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'revision_number' => 'integer',
        'mandatory_route' => 'array',
        'filters' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flightInformationRegion(): BelongsTo
    {
        return $this->belongsTo(FlightInformationRegion::class);
    }

    public function notifiedFlightInformationRegions(): BelongsToMany
    {
        return $this->belongsToMany(FlightInformationRegion::class)
            ->withTimestamps();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeStartsBetween(Builder $query, Carbon $periodStart, Carbon $periodEnd): Builder
    {
        return $query->where('start_time', '>=', $periodStart)
            ->where('start_time', '<=', $periodEnd);
    }

    public function scopeEndsBetween(Builder $query, Carbon $periodStart, Carbon $periodEnd): Builder
    {
        return $query->where('end_time', '>=', $periodStart)
            ->where('end_time', '<=', $periodEnd);
    }

    public function scopeActiveThroughout(Builder $query, Carbon $periodStart, Carbon $periodEnd): Builder
    {
        return $query->where('start_time', '<=', $periodStart)
            ->where('end_time', '>=', $periodEnd);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('start_time', '<=', $now)
            ->where('end_time', '>', $now);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('end_time', '<', Carbon::now());
    }

    public function scopeExpiredRecently(Builder $query): Builder
    {
        return $query->where('end_time', '<', Carbon::now())
            ->where('end_time', '>', Carbon::now()->subMinutes(15));
    }

    public function isActive(): bool
    {
        return Carbon::now()->between($this->start_time, $this->end_time);
    }

    public function isMandatoryRoute(): bool
    {
        return $this->type === FlowMeasureType::MANDATORY_ROUTE;
    }

    public function scopeFlightInformationRegion(
        Builder $query,
        FlightInformationRegion $flightInformationRegion
    ): Builder {
        return $query->where('flight_information_region_id', $flightInformationRegion->id);
    }

    public function divisionDiscordNotifications(): BelongsToMany
    {
        return $this->belongsToMany(DivisionDiscordNotification::class)
            ->withPivot(['discord_notification_type_id', 'notified_as'])
            ->withTimestamps();
    }

    public function discordNotifications(): BelongsToMany
    {
        return $this->belongsToMany(DiscordNotification::class)
            ->withPivot(['discord_notification_type_id', 'notified_as'])
            ->withTimestamps();
    }

    public function discordNotificationsOfType(array $types): BelongsToMany
    {
        return $this->discordNotifications()
            ->wherePivotIn(
                'discord_notification_type_id',
                DiscordNotificationType::whereIn(
                    'type',
                    $types
                )->pluck('id')
            );
    }

    public function notifiedDivisionNotifications(): BelongsToMany
    {
        return $this->divisionNotificationsOfType([DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED]);
    }

    public function notifiedEcfmpNotifications(): BelongsToMany
    {
        return $this->discordNotificationsOfType([DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED]);
    }

    public function activatedDivisionNotifications(): BelongsToMany
    {
        return $this->divisionNotificationsOfType([DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED]);
    }

    public function withdrawnDivisionNotifications(): BelongsToMany
    {
        return $this->divisionNotificationsOfType([DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN]);
    }

    public function expiredDivisionNotifications(): BelongsToMany
    {
        return $this->divisionNotificationsOfType([DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED]);
    }

    public function withdrawnAndExpiredDivisionNotifications(): BelongsToMany
    {
        return $this->divisionNotificationsOfType(
            [
                DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN,
                DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED,
            ]
        );
    }

    public function activatedAndNotifiedDivisionNotifications(): BelongsToMany
    {
        return $this->divisionNotificationsOfType(
            [
                DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
            ]
        );
    }

    public function scopeWithoutEcfmpNotificationOfTypeForIdentifier(
        Builder $query,
        DiscordNotificationTypeEnum $type
    ): Builder {
        return $query->whereDoesntHave('discordNotifications', function (Builder $query) use ($type) {
            $query->where('discord_notification_type_id', DiscordNotificationType::where('type', $type)->firstOrFail()->id)
                ->whereRaw('`notified_as` = `identifier`');
        });
    }

    public function scopeWithoutEcfmpNotificationOfType(
        Builder $query,
        DiscordNotificationTypeEnum $type
    ): Builder {
        return $this->scopeWithoutEcfmpNotificationOfTypes($query, [$type]);
    }

    public function scopeWithoutEcfmpNotificationOfTypes(
        Builder $query,
        array $types
    ): Builder {
        return $query->whereDoesntHave('discordNotifications', function (Builder $query) use ($types) {
            $query->whereIn('discord_notification_type_id', DiscordNotificationType::whereIn('type', $types)->pluck('id'));
        });
    }

    public function scopeWithEcfmpNotificationOfType(
        Builder $query,
        DiscordNotificationTypeEnum $type
    ): Builder {
        return $this->scopeWithEcfmpNotificationOfTypes($query, [$type]);
    }

    public function scopeWithEcfmpNotificationOfTypes(
        Builder $query,
        array $types
    ): Builder {
        return $query->whereHas('discordNotifications', function (Builder $query) use ($types) {
            $query->whereIn('discord_notification_type_id', DiscordNotificationType::whereIn('type', $types)->pluck('id'));
        });
    }

    private function divisionNotificationsOfType(array $types): BelongsToMany
    {
        return $this->divisionDiscordNotifications()
            ->wherePivotIn(
                'discord_notification_type_id',
                DiscordNotificationType::whereIn(
                    'type',
                    $types
                )->pluck('id')
            );
    }

    public function filtersByType(FilterType $filterType): array
    {
        return array_values(
            array_filter(
                $this->filters,
                fn (array $filter) => FilterType::tryFrom($filter['type']) === $filterType
            )
        );
    }

    public function getTypeAttribute(): FlowMeasureType
    {
        return FlowMeasureType::from($this->attributes['type']);
    }

    public function extraFilters(): array
    {
        return array_filter(
            $this->filters,
            fn (array $filter) => !in_array(
                FilterType::tryFrom($filter['type']),
                [FilterType::DEPARTURE_AIRPORTS, FilterType::ARRIVAL_AIRPORTS]
            )
        );
    }

    public function scopeNotified(Builder $builder): Builder
    {
        return $builder->where('start_time', '<', Carbon::now()->addDay())
            ->where('start_time', '>', Carbon::now());
    }

    public function scopeEndTimeWithinOneDay(Builder $builder): Builder
    {
        return $builder->where('start_time', '<', Carbon::now())
            ->where('end_time', '>', Carbon::now()->subDay());
    }

    public function status(): Attribute
    {
        return new Attribute(function () {
            if ($this->trashed()) {
                return FlowMeasureStatus::DELETED;
            }

            if ($this->end_time->lt(now())) {
                return FlowMeasureStatus::EXPIRED;
            }

            if ($this->start_time->lt(now())) {
                return FlowMeasureStatus::ACTIVE;
            }

            return FlowMeasureStatus::NOTIFIED;
        });
    }
}
