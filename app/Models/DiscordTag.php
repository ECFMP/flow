<?php

namespace App\Models;

use App\Discord\Message\Tag\TagProviderInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscordTag extends Model implements TagProviderInterface
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

    public function rawTagString(): string
    {
        return $this->tag;
    }
}
