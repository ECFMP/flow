<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Location\Coordinate;

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
        'estimated_arrival_time',
        'distance_to_destination',
    ];

    protected $casts = [
        'cid' => 'integer',
        'altitude' => 'integer',
        'cruise_altitude' => 'integer',
        'vatsim_pilot_status_id' => VatsimPilotStatus::class,
        'distance_to_destination' => 'double',
        'estimated_arrival_time' => 'datetime',
    ];

    public function getCoordinate(): Coordinate
    {
        return new Coordinate($this->latitude, $this->longitude);
    }
}
