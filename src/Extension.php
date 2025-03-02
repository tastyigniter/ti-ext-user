<?php

namespace Igniter\User;

use Igniter\Flame\Igniter;
use Igniter\Main\Facades\Auth;
use Igniter\Main\Models\Customer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;

class Extension extends \Igniter\System\Classes\BaseExtension
{
    public function register()
    {
        $this->registerEventGlobalParams();
        //        $this->registerRequestRebindHandler();
    }

    public function boot()
    {
        $this->configureRateLimiting();

        Event::listen('igniter.user.register', function (Customer $customer, array $data) {
            Notifications\CustomerRegisteredNotification::make()->subject($customer)->broadcast();
        });
    }

    public function registerAutomationRules()
    {
        return [
            'events' => [
                'igniter.user.register' => \Igniter\User\AutomationRules\Events\CustomerRegistered::class,
            ],
            'actions' => [],
            'conditions' => [
                \Igniter\User\AutomationRules\Conditions\CustomerAttribute::class,
            ],
        ];
    }

    public function registerComponents()
    {
        return [
            \Igniter\User\Components\Session::class => [
                'code' => 'session',
                'name' => 'lang:igniter.user::default.session.component_title',
                'description' => 'lang:igniter.user::default.session.component_desc',
            ],
            \Igniter\User\Components\Account::class => [
                'code' => 'account',
                'name' => 'lang:igniter.user::default.account.component_title',
                'description' => 'lang:igniter.user::default.account.component_desc',
            ],
            \Igniter\User\Components\ResetPassword::class => [
                'code' => 'resetPassword',
                'name' => 'lang:igniter.user::default.reset.component_title',
                'description' => 'lang:igniter.user::default.reset.component_desc',
            ],
            \Igniter\User\Components\AddressBook::class => [
                'code' => 'accountAddressBook',
                'name' => 'lang:igniter.user::default.addressbook.component_title',
                'description' => 'lang:igniter.user::default.addressbook.component_desc',
            ],
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'igniter.user::mail.password_reset' => 'lang:igniter.user::default.text_mail_password_reset',
            'igniter.user::mail.password_reset_request' => 'lang:igniter.user::default.text_mail_password_reset_request',
            'igniter.user::mail.registration' => 'lang:igniter.user::default.text_mail_registration',
            'igniter.user::mail.registration_alert' => 'lang:igniter.user::default.text_mail_registration_alert',
            'igniter.user::mail.activation' => 'lang:igniter.user::default.text_mail_activation',
        ];
    }

    protected function registerEventGlobalParams()
    {
        if (class_exists(\Igniter\Automation\Classes\EventManager::class)) {
            resolve(\Igniter\Automation\Classes\EventManager::class)->registerCallback(function ($manager) {
                $manager->registerGlobalParams([
                    'customer' => Auth::customer(),
                ]);
            });
        }
    }

    protected function registerRequestRebindHandler()
    {
        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                if (!Igniter::runningInAdmin()) {
                    return $app['admin.auth']->getUser();
                }

                return $app['main.auth']->user();
            });
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('web', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->getKey() ?: $request->ip());
        });

        if (Igniter::runningInAdmin()) {
            return;
        }

        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->appendMiddlewareToGroup('web', \Igniter\User\Middleware\ThrottleRequests::class);

        Event::listen('igniter.user.beforeThrottleRequest', function ($request, $params) {
            $handler = str_after($request->header('x-igniter-request-handler'), '::');
            if (in_array($handler, [
                'onLogin',
                'onRegister',
                'onActivate',
                'onForgotPassword',
                'onResetPassword',
            ])) {
                $params->maxAttempts = 6;
                $params->decayMinutes = 1;

                return true;
            }
        });
    }
}
