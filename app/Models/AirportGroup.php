<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AirportGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function airports(): BelongsToMany
    {
        return $this->belongsToMany(Airport::class);
    }
}
