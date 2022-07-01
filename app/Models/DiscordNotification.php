<?php

namespace App\Models;

use App\Enums\DiscordNotificationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscordNotification extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'division_discord_webhook_id',
        'content',
        'embeds',
    ];

    protected $casts = [
        'type' => DiscordNotificationType::class,
        'embeds' => 'array',
    ];

    public function flowMeasure(): BelongsToMany
    {
        return $this->belongsToMany(FlowMeasure::class)
            ->withPivot(['type', 'notified_as'])
            ->withTimestamps();
    }

    public function scopeType(Builder $query, DiscordNotificationType $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('type', $types);
    }

    public function divisionDiscordWebhook(): BelongsTo
    {
        return $this->belongsTo(DivisionDiscordWebhook::class);
    }
}
