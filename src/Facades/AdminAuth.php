<?php

namespace Igniter\User\Facades;

use Illuminate\Support\Facades\Facade;

class AdminAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @see \Igniter\User\Auth\UserGuard
     */
    protected static function getFacadeAccessor()
    {
        return 'admin.auth';
    }
}
