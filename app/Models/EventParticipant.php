<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    protected $fillable = [
        'cid',
        'origin',
        'destination',
    ];

    public $timestamps = false;
}
