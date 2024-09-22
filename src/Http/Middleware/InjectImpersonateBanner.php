<?php

namespace Igniter\User\Http\Middleware;

use Igniter\Flame\Igniter;
use Igniter\User\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InjectImpersonateBanner
{
    public function handle($request, \Closure $next): Response
    {
        $response = $next($request);

        if (!$request->routeIs('igniter.theme.*') || Igniter::runningInAdmin()) {
            return $response;
        }

        if (!Auth::check() || !Auth::isImpersonator()) {
            return $response;
        }

        $this->injectBanner($response);

        return $response;
    }

    protected function injectBanner(Response $response): void
    {
        $content = $response->getContent();
        $banner = view('igniter.user::_partials.impersonate_banner')->render();
        $pos = strripos($content, '</body>');
        if ($pos !== false) {
            $content = substr($content, 0, $pos).$banner.substr($content, $pos);
        } else {
            $content .= $banner;
        }

        $response->setContent($content);
    }
}
