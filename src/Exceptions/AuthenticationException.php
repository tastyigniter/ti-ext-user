<?php

namespace Igniter\User\Exceptions;

use Igniter\Admin\Helpers\AdminHelper;
use Illuminate\Auth\AuthenticationException as Exception;

class AuthenticationException extends Exception
{
    public function render($request)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $this->getMessage()], 403)
            : AdminHelper::redirectGuest('login');
    }
}
