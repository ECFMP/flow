<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AirportGroup extends Model
{
    protected $fillable = [
        'name',
    ];

    public function airports(): BelongsToMany
    {
        return $this->belongsToMany(Airport::class)->withTimestamps();
    }
}
