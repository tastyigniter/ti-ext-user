<?php

declare(strict_types=1);

namespace Igniter\User\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\Template;
use Igniter\Admin\Helpers\AdminHelper;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

class Login extends AdminController
{
    public ?string $bodyClass = 'page-login';

    public static bool $skipRouteRegister = true;

    public function __construct()
    {
        $this->middleware('throttle:'.config('igniter-auth.rateLimiter', '6,1'));
        parent::__construct();
    }

    public function index(): RedirectResponse|string
    {
        // Redirect /admin to /admin/login
        if (!request()->routeIs('igniter.admin.login')) {
            return AdminHelper::redirect('login');
        }

        if (AdminAuth::isLogged()) {
            return AdminHelper::redirect('dashboard');
        }

        Template::setTitle(lang('igniter::admin.login.text_title'));

        return $this->makeView('auth.login');
    }

    public function reset(): RedirectResponse|string
    {
        if (AdminAuth::isLogged()) {
            return AdminHelper::redirect('dashboard');
        }

        $code = input('code', '');
        if (strlen((string) $code) && !User::query()->whereResetCode($code)->first()) {
            flash()->error(lang('igniter::admin.login.alert_failed_reset'));

            return AdminHelper::redirect('login');
        }

        Template::setTitle(lang('igniter::admin.login.text_password_reset_title'));

        $this->vars['resetCode'] = input('code');

        return $this->makeView('auth.reset');
    }

    public function onLogin(): RedirectResponse
    {
        $data = $this->validate(post(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [], [
            'email' => lang('igniter::admin.login.label_email'),
            'password' => lang('igniter::admin.login.label_password'),
        ]);

        Event::dispatch('igniter.admin.beforeAuthenticate', [$data]);

        if (!Auth::check() && !AdminAuth::attempt(array_only($data, ['email', 'password']), true)) {
            throw ValidationException::withMessages(['email' => lang('igniter::admin.login.alert_login_failed')]);
        }

        session()->regenerate();

        return ($redirectUrl = input('redirect'))
            ? AdminHelper::redirect($redirectUrl)
            : AdminHelper::redirectIntended('dashboard');
    }

    public function onRequestResetPassword(): RedirectResponse
    {
        $data = $this->validate(post(), [
            'email' => ['required', 'email:filter', 'max:96'],
        ], [], [
            'email' => lang('igniter::admin.label_email'),
        ]);

        if ($user = User::query()->whereEmail($data['email'])->first()) {
            /** @var User $user */
            $user->resetPassword();
            $user->mailSendResetPasswordRequest([
                'reset_link' => admin_url('login/reset?code='.$user->reset_code),
            ]);
        }

        flash()->success(lang('igniter::admin.login.alert_email_sent'));

        return AdminHelper::redirect('login');
    }

    public function onResetPassword(): RedirectResponse
    {
        $data = $this->validate(post(), [
            'code' => ['required'],
            'password' => ['required', 'min:6', 'max:32', 'same:password_confirm'],
            'password_confirm' => ['required'],
        ], [], [
            'code' => lang('igniter::admin.login.label_reset_code'),
            'password' => lang('igniter::admin.login.label_password'),
            'password_confirm' => lang('igniter::admin.login.label_password_confirm'),
        ]);

        $code = array_get($data, 'code');
        /** @var null|User $user */
        $user = User::query()->whereResetCode($code)->first();

        if (!$user || !$user->completeResetPassword($data['code'], $data['password'])) {
            throw ValidationException::withMessages(['password' => lang('igniter::admin.login.alert_failed_reset')]);
        }

        $user->mailSendResetPassword(['login_link' => admin_url('login')]);

        flash()->success(lang('igniter::admin.login.alert_success_reset'));

        return AdminHelper::redirect('login');
    }
}
