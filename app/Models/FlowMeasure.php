<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowMeasure extends Model
{
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

    public function owner(): BelongsTo
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
}
