<?php

namespace Igniter\User\Http\Middleware;

use Closure;

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
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = config('igniter-auth.guards.admin');

        if (!empty($guard)) {
            $guards[] = $guard;
        }

        return parent::handle($request, $next, ...$guards);
    }
}
