<?php

namespace App\Observers;

use App\Events\ActivityLogged;
use App\Helpers\LogHelper;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        event(new ActivityLogged(
            $user,
            "Added new user: {$user->name} (ID: {$user->id})",
            'User',
        ));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $properties = LogHelper::format($user);

        if (empty($properties) && !$user->wasChanged('password')) {
            return;
        }

        if ($user->wasChanged('password')) {
            $properties['password'] = 'Password updated';
        }

        event(new ActivityLogged(
            $user, 
            "Updated user {$user->name} (ID: {$user->id})",
            'User', 
            $properties));
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        event(new ActivityLogged(
            $user,
            "Deleted user: {$user->name} (ID: {$user->id}) (email: {$user->email})",
            'User',
        ));
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
