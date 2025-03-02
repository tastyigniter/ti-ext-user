<?php

namespace Igniter\User\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Igniter\Flame\Igniter;
use Igniter\User\Facades\AdminAuth;
use Illuminate\Support\Facades\Cache;

class LogUserLastSeen
{
    public function handle($request, Closure $next)
    {
        if (Igniter::hasDatabase() && AdminAuth::check()) {
            $cacheKey = 'is-online-user-'.AdminAuth::getId();
            $expireAt = Carbon::now()->addMinutes(2);
            Cache::remember($cacheKey, $expireAt, function () {
                return AdminAuth::user()->updateLastSeen(Carbon::now()->addMinutes(5));
            });
        }

        return $next($request);
    }
}
