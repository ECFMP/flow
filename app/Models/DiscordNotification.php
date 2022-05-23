<?php

namespace App\Models;

use App\Enums\DiscordNotificationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscordNotification extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'flow_measure_id',
        'type',
        'content',
        'embeds',
    ];

    protected $casts = [
        'type' => DiscordNotificationType::class,
        'embeds' => 'array',
    ];

    public function flowMeasure(): BelongsTo
    {
        return $this->belongsTo(FlowMeasure::class);
    }

    public function scopeType(Builder $query, DiscordNotificationType $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('type', $types);
    }
}
