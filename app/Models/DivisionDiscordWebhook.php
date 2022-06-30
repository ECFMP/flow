<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionDiscordWebhook extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'url',
        'description',
        'tag',
    ];

    public function flightInformationRegions(): BelongsToMany
    {
        return $this->belongsToMany(FlightInformationRegion::class);
    }
}
