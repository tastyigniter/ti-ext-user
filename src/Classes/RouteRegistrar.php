<?php

namespace Igniter\User\Classes;

use Igniter\Flame\Support\Facades\Igniter;
use Igniter\User\Http\Controllers\Login;
use Igniter\User\Http\Controllers\Logout;
use Illuminate\Routing\Router;

class RouteRegistrar
{
    public function __construct(protected Router $router) {}

    public function all()
    {
        $this->router
            ->middleware(config('igniter-routes.middleware', []))
            ->domain(config('igniter-routes.adminDomain'))
            ->prefix(Igniter::adminUri())
            ->group(function(Router $router) {
                $router->any('/', [Login::class, 'index'])->name('igniter.admin');
                $router->any('/login', [Login::class, 'index'])->name('igniter.admin.login');
                $router->any('/login/reset/{slug?}', [Login::class, 'reset'])->name('igniter.admin.reset');
                $router->any('/logout', [Logout::class, 'index'])->name('igniter.admin.logout');
            });
    }
}

