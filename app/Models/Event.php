<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_start',
        'date_end',
        'flight_information_region_id',
        'vatcan_code'
    ];

    protected $dates = [
        'date_start',
        'date_end',
    ];

    public function flightInformationRegion(): BelongsTo
    {
        return $this->belongsTo(FlightInformationRegion::class);
    }

    public function flowMeasures(): HasMany
    {
        return $this->hasMany(FlowMeasure::class);
    }

    protected function nameDate(): Attribute
    {
        return new Attribute(
            fn () => "{$this->name} [{$this->date_start->format('M j, Y')}]",
        );
    }
}
