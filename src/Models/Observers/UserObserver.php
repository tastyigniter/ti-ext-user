<?php

namespace Igniter\User\Models\Observers;

use Igniter\User\Models\User;

class UserObserver
{
    public function deleting(User $user)
    {
        $user->groups()->detach();
        $user->locations()->detach();
    }

    public function saved(User $user)
    {
        $user->restorePurgedValues();

        if ($user->status && is_null($user->is_activated)) {
            $user->completeActivation($user->getActivationCode());
        }
    }
}
