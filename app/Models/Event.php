<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
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
}
