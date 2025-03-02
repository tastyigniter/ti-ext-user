<?php

namespace Igniter\User\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request):mixed $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Igniter\User\Exceptions\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $guard = config('igniter-auth.guards.admin');

            if (!empty($guard)) {
                $guards[] = $guard;
            }

            return parent::handle($request, $next, ...$guards);
        } catch (AuthenticationException $e) {
            throw new \Igniter\User\Exceptions\AuthenticationException(lang('igniter::admin.alert_user_not_logged_in'), $e->guards());
        }
    }
}
