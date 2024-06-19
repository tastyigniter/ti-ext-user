<?php

namespace Igniter\User\Actions;

use Igniter\Flame\Exception\FlashException;
use Igniter\User\Facades\Auth;
use Illuminate\Support\Facades\Event;

class LoginUser
{
    public function __construct(public array $credentials, public bool $remember = true) {}

    public function handle()
    {
        Event::fire('igniter.user.beforeAuthenticate', [$this, $this->credentials]);

        if (!Auth::attempt($this->credentials, $this->remember)) {
            throw new FlashException(lang('igniter.user::default.login.alert_invalid_login'));
        }

        session()->regenerate();

        Event::fire('igniter.user.login', [$this], true);
    }
}
