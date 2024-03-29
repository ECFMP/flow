<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use App\Enums\RoleKey;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
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
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Event $event)
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
            RoleKey::EVENT_MANAGER,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Event $event)
    {
        if ($event->date_end > now()) {
            return $event->flightInformationRegion
                ->users()
                ->whereUserId($user->id)
                ->exists() && in_array($user->role->key, [
                    RoleKey::EVENT_MANAGER,
                    RoleKey::FLOW_MANAGER,
                ]) || in_array($user->role->key, [
                    RoleKey::SYSTEM,
                    RoleKey::NMT,
                ]);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Event $event)
    {
        return $event->flightInformationRegion
            ->users()
            ->whereUserId($user->id)
            ->exists() && in_array($user->role->key, [
                RoleKey::EVENT_MANAGER,
                RoleKey::FLOW_MANAGER,
            ]) || in_array($user->role->key, [
                RoleKey::SYSTEM,
                RoleKey::NMT,
            ]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Event $event)
    {
        return $event->flightInformationRegion
            ->users()
            ->whereUserId($user->id)
            ->exists() && in_array($user->role->key, [
                RoleKey::EVENT_MANAGER,
                RoleKey::FLOW_MANAGER,
            ]) || in_array($user->role->key, [
                RoleKey::SYSTEM,
                RoleKey::NMT,
            ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Event $event)
    {
        return $event->flightInformationRegion
            ->users()
            ->whereUserId($user->id)
            ->exists() && in_array($user->role->key, [
                RoleKey::EVENT_MANAGER,
                RoleKey::FLOW_MANAGER,
            ]) || in_array($user->role->key, [
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
