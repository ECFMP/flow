<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AirportGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function airports(): BelongsToMany
    {
        return $this->belongsToMany(Airport::class)->withTimestamps();
    }

    protected function airportCodes(): Attribute
    {
        return new Attribute(
            fn () => $this->airports->sortBy('icao_code')
                ->pluck('icao_code')
                ->join(', '),
        );
    }

    protected function nameCodes(): Attribute
    {
        return new Attribute(
            fn () => "{$this->name} [{$this->airport_codes}]",
        );
    }
}
