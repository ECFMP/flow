<?php

namespace App\Models;

use App\Enums\RoleKey;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    public $incrementing = false;

    public $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'role_id',
    ];

    protected $hidden = [
        'token',
        'refresh_token',
        'refresh_token_expires_at',
    ];

    protected $casts = [
        'role_id' => 'integer',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function flightInformationRegions(): BelongsToMany
    {
        return $this->belongsToMany(FlightInformationRegion::class);
    }

    public function flowMeasures(): HasMany
    {
        return $this->hasMany(FlowMeasure::class);
    }

    public function canAccessFilament(): bool
    {
        return true;
    }

    public function getAssignableRoles(): array
    {
        switch ($this->role->key) {
            case RoleKey::SYSTEM:
                return RoleKey::cases();
            case RoleKey::NMT:
                return [
                    RoleKey::NMT,
                    RoleKey::FLOW_MANAGER,
                    RoleKey::EVENT_MANAGER,
                    RoleKey::USER,
                ];
            default:
                return [];
        }
    }

    public function nameAndCid(): Attribute
    {
        return Attribute::get(fn () => sprintf('%s (%d)', $this->name, $this->id));
    }
}
