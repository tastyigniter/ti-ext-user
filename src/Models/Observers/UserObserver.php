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
}
