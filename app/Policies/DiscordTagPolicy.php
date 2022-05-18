<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleKey;
use App\Models\DiscordTag;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscordTagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DiscordTag  $discordTag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, DiscordTag $discordTag)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DiscordTag  $discordTag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, DiscordTag $discordTag)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DiscordTag  $discordTag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DiscordTag $discordTag)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DiscordTag  $discordTag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, DiscordTag $discordTag)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DiscordTag  $discordTag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, DiscordTag $discordTag)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    public function deleteAny()
    {
        return false;
    }

    public function detachAny()
    {
        return false;
    }
}
