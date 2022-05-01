<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowMeasure extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
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

    protected $dates = [
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'mandatory_route' => 'array',
        'filters' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flightInformationRegion(): BelongsTo
    {
        return $this->belongsTo(FlightInformationRegion::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('start_time', '<=', $now)
            ->where('end_time', '>', $now);
    }

    public function isMandatoryRoute(): bool
    {
        return $this->type === 'mandatory_route';
    }

    public function scopeFlightInformationRegion(
        Builder $query,
        FlightInformationRegion $flightInformationRegion
    ): Builder {
        return $query->where('flight_information_region_id', $flightInformationRegion->id);
    }
}
