<?php

namespace Igniter\User\Http\Controllers;

use Igniter\User\Facades\AdminAuth;

class Logout extends \Igniter\Admin\Classes\AdminController
{
    protected $requireAuthentication = false;

    public static bool $skipRouteRegister = true;

    public function index()
    {
        if (AdminAuth::isImpersonator()) {
            AdminAuth::stopImpersonate();
        } else {
            AdminAuth::logout();

            session()->invalidate();

            session()->regenerateToken();
        }

        flash()->success(lang('igniter::admin.login.alert_success_logout'));

        return $this->redirect('login');
    }
}
