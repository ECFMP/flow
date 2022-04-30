<?php

namespace App\Models;

use App\Enums\RoleKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'key',
        'description',
    ];

    protected $casts = [
        'key' => RoleKey::class,
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
