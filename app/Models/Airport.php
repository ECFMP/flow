<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'icao_code',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(AirportGroup::class);
    }

    public function airportGroup(): BelongsToMany
    {
        return $this->groups();
    }

    protected function icaoCode(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtoupper($value)
        );
    }
}
