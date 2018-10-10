<?php

namespace Igniter\User\Components;

use Auth;
use Event;
use Main\Traits\HasPageOptions;
use Redirect;
use Request;
use System\Classes\BaseComponent;

class Session extends BaseComponent
{
    use HasPageOptions;

    public function initialize()
    {
        if (Request::ajax() AND !$this->checkSecurity()) {
            abort(403, 'Access denied');
        }
    }

    public function defineProperties()
    {
        return [
            'security' => [
                'label' => 'Who can access this page',
                'type' => 'string',
                'default' => 'all',
            ],
            'redirectPage' => [
                'label' => 'Page name to redirect to when access is restricted',
                'type' => 'select',
                'default' => 'home',
                'options' => [static::class, 'getPageOptions'],
            ],
        ];
    }

    public function onRun()
    {
        if (!$this->checkSecurity()) {
            return Redirect::guest($this->controller->pageUrl($this->property('redirectPage')));
        }

        $this->page['customer'] = $this->customer();
    }

    public function customer()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    public function onLogout()
    {
        $user = Auth::getUser();

        Auth::logout();

        if ($user) {
            Event::fire('igniter.user.logout', [$user]);
        }

        $url = post('redirect', Request::fullUrl());

        flash()->success(lang('igniter.user::default.alert_logout_success'));

        return Redirect::to($url);
    }

    protected function checkSecurity()
    {
        $allowedGroup = $this->property('security', 'all');
        $isAuthenticated = Auth::check();
        if ($allowedGroup == 'customer' AND !$isAuthenticated) {
            flash()->danger(lang('igniter.user::default.login.alert_expired_login'));

            return FALSE;
        }

        if ($allowedGroup == 'guest' AND $isAuthenticated) {
            return FALSE;
        }

        return TRUE;
    }
}
