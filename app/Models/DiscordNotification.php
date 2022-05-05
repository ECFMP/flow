<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscordNotification extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'flow_measure_id',
        'content',
    ];

    public function flowMeasure(): BelongsTo
    {
        return $this->belongsTo(FlowMeasure::class);
    }
}
