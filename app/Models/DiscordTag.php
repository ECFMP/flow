<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordTag extends Model
{
    protected $fillable = [
        'tag',
        'description',
    ];
}
