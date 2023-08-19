<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'date_start',
        'date_end',
        'flight_information_region_id',
        'vatcan_code'
    ];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    public function flightInformationRegion(): BelongsTo
    {
        return $this->belongsTo(FlightInformationRegion::class);
    }

    public function flowMeasures(): HasMany
    {
        return $this->hasMany(FlowMeasure::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('date_start', '<=', $now)
            ->where('date_end', '>', $now);
    }

    protected function nameDate(): Attribute
    {
        return new Attribute(
            fn () => "{$this->name} [{$this->date_start->format('M j, Y')}]",
        );
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }
}
