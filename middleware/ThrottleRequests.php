<?php

namespace Igniter\User\Middleware;

use Closure;
use Illuminate\Support\Facades\Event;

class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        $params = new \stdClass;
        $params->maxAttempts = $maxAttempts;
        $params->decayMinutes = $decayMinutes;
        $params->prefix = $prefix;

        if ($this->shouldThrottleRequest($request, $params))
            return parent::handle($request, $next, $params->maxAttempts, $params->decayMinutes, $params->prefix);

        return $next($request);
    }

    protected function shouldThrottleRequest($request, $params)
    {
        return Event::fire('igniter.user.beforeThrottleRequest', [$request, $params], true);
    }
}
