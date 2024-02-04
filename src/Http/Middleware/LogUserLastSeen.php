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
            foreach (['admin.auth', 'main.auth'] as $authAlias) {
                $authService = resolve($authAlias);
                if ($authService->check()) {
                    $cacheKey = 'is-online-'.str_replace('.', '-', $authAlias).'-user-'.$authService->getId();
                    $expireAt = Carbon::now()->addMinutes(2);
                    Cache::remember($cacheKey, $expireAt, function () use ($authService) {
                        return $authService->user()->updateLastSeen(Carbon::now());
                    });
                }
            }
        }

        return $next($request);
    }
}
