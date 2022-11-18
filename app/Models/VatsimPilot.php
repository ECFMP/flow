<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatsimPilot extends Model
{
    use HasFactory;

    protected $fillable = [
        'callsign',
        'cid',
        'departure_airport',
        'destination_airport',
        'altitude',
        'cruise_altitude',
        'route_string',
        'vatsim_pilot_status_id',
    ];

    protected $casts = [
        'cid' => 'integer',
        'altitude' => 'integer',
        'cruise_altitude' => 'integer',
        'vatsim_pilot_status_id' => VatsimPilotStatus::class,
    ];
}
