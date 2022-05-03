<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlightInformationRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function discordTags(): BelongsToMany
    {
        return $this->belongsToMany(DiscordTag::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function flowMeasures(): HasMany
    {
        return $this->hasMany(FlowMeasure::class);
    }

    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
            set: fn($value) => strtoupper($value)
        );
    }

    protected function identifierName(): Attribute
    {
        return new Attribute(
            fn() => "{$this->identifier} | {$this->name}",
        );
    }
}
