<?php

namespace App\Models;

use App\Enums\DiscordNotificationTypeEnum as DiscordNotificationTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DiscordNotificationType extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'type',
    ];

    protected $casts = [
        'type' => DiscordNotificationTypeEnum::class,
        'embeds' => 'array',
    ];

    public function scopeType(Builder $query, DiscordNotificationType $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('type', $types);
    }

    public static function fromEnum(DiscordNotificationTypeEnum $type): DiscordNotificationType
    {
        return static::where('type', $type)
            ->firstOrFail();
    }

    public static function idFromEnum(DiscordNotificationTypeEnum $type): int
    {
        return static::fromEnum($type)->id;
    }
}
