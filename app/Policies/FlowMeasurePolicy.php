<?php

namespace App\Policies;

use App\Enums\RoleKey;
use App\Models\FlowMeasure;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlowMeasurePolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FlowMeasure  $flowMeasure
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, FlowMeasure $flowMeasure)
    {
        return true;
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
            RoleKey::FLOW_MANAGER,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FlowMeasure  $flowMeasure
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FlowMeasure $flowMeasure)
    {
        return $flowMeasure->user == $user || in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FlowMeasure  $flowMeasure
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FlowMeasure $flowMeasure)
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
     * @param  \App\Models\FlowMeasure  $flowMeasure
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, FlowMeasure $flowMeasure)
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
     * @param  \App\Models\FlowMeasure  $flowMeasure
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, FlowMeasure $flowMeasure)
    {
        return in_array($user->role->key, [
            RoleKey::SYSTEM,
            RoleKey::NMT,
        ]);
    }
}
