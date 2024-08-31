<?php

namespace Igniter\User\Actions;

use Igniter\User\Facades\Auth;
use Illuminate\Support\Facades\Event;

class LogoutUser
{
    public function handle()
    {
        $user = Auth::getUser();

        if (Auth::isImpersonator()) {
            Auth::stopImpersonate();
        } else {
            Auth::logout();

            session()->invalidate();

            session()->regenerateToken();

            if ($user) {
                Event::fire('igniter.user.logout', [$user]);
            }
        }

        flash()->success(lang('igniter.user::default.alert_logout_success'));
    }
}
