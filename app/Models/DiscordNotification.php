<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscordNotification extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'remote_id',
    ];

    public function flowMeasure(): BelongsToMany
    {
        return $this->belongsToMany(FlowMeasure::class)
            ->withPivot(['notified_as', 'discord_notification_type_id'])
            ->withTimestamps();
    }
}
