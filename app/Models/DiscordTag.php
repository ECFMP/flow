<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscordTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag',
        'description',
    ];

    public function flightInformationRegions(): BelongsToMany
    {
        return $this->belongsToMany(FlightInformationRegion::class)->withTimestamps();
    }
}
